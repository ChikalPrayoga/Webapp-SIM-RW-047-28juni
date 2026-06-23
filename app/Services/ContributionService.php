<?php

namespace App\Services;

use App\Models\CatatanIuranWarga;
use App\Models\IuranType;
use App\Models\KartuKeluarga;
use App\Models\User;
use App\Enums\PaymentStatus;
use App\Enums\TransactionCategory;
use App\Events\ContributionRecorded;
use App\Events\ContributionValidated;
use App\Events\ContributionInvalidated;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;

class ContributionService
{
    private LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Mencatat iuran warga yang diterima secara tunai oleh Ketua RT.
     *
     * @param array{
     *   no_kk: string,
     *   iuran_type_id: int,
     *   nominal: float|int,
     *   periode_bulan: int,
     *   periode_tahun: int,
     *   tanggal_pembayaran: string
     * } $data
     * @param int $userId
     * @return CatatanIuranWarga
     * @throws InvalidArgumentException
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     * @throws LogicException
     */
    public function recordContribution(array $data, int $userId): CatatanIuranWarga
    {
        // 1. Validasi Domain Basic
        $this->validateDomainRules($data);

        // 2. Load Data dan Validasi Eksistensi
        $user = User::where('user_id', $userId)->firstOrFail();
        $kartuKeluarga = KartuKeluarga::where('no_kk', $data['no_kk'])->firstOrFail();
        $iuranType = IuranType::findOrFail($data['iuran_type_id']);

        // 3. Validasi Keaktifan Iuran Type
        if (!$iuranType->is_active) {
            throw new LogicException("Jenis Iuran tidak aktif.");
        }

        // 4. Validasi RT Scope
        $userArea = $user->position?->area_code;
        if (!empty($userArea) && $userArea !== $kartuKeluarga->rt_code) {
            throw new AuthorizationException("Ketua RT hanya dapat mencatat iuran untuk warga di wilayahnya sendiri.");
        }

        // 5. Pencegahan Duplikasi Pencatatan
        $exists = CatatanIuranWarga::where('no_kk', $data['no_kk'])
            ->where('iuran_type_id', $data['iuran_type_id'])
            ->where('periode_bulan', $data['periode_bulan'])
            ->where('periode_tahun', $data['periode_tahun'])
            ->exists();

        if ($exists) {
            throw new LogicException("Iuran untuk periode tersebut sudah dicatat.");
        }

        // 6. Transaction Boundary (Atomic Commit)
        return DB::transaction(function () use ($data, $user, $kartuKeluarga, $iuranType) {
            
            // A. Buat Catatan Administrasi
            $catatanIuran = CatatanIuranWarga::create([
                'no_kk' => $data['no_kk'],
                'iuran_type_id' => $data['iuran_type_id'],
                'nominal' => $data['nominal'],
                'periode_bulan' => $data['periode_bulan'],
                'periode_tahun' => $data['periode_tahun'],
                'tanggal_pembayaran' => Carbon::parse($data['tanggal_pembayaran'])->toDateString(),
                'recorded_by_user_id' => $user->user_id,
                'status' => PaymentStatus::PENDING,
            ]);

            // B. Delegasi Mutasi Kas ke LedgerService
            $this->ledgerService->createIncome([
                'rt_code' => $kartuKeluarga->rt_code,
                'category' => TransactionCategory::IURAN,
                'amount' => $data['nominal'],
                'description' => "Pembayaran {$iuranType->name} Periode {$data['periode_bulan']}/{$data['periode_tahun']} (KK: {$data['no_kk']})",
                'transaction_date' => $data['tanggal_pembayaran'],
                'reference_type' => CatatanIuranWarga::class,
                'reference_id' => $catatanIuran->iuran_id,
                'created_by_user_id' => $user->user_id,
            ]);

            // C. Eager load untuk mencegah N+1 di Listener Event
            $catatanIuran->load(['kartuKeluarga', 'iuranType', 'recorder']);

            // D. Dispatch Event
            DB::afterCommit(function () use ($catatanIuran) {
                event(new ContributionRecorded($catatanIuran));
            });

            return $catatanIuran;
        });
    }

    /**
     * Memvalidasi catatan iuran (audit fisik oleh Bendahara RW).
     *
     * @param int $id
     * @param int $userId
     * @return CatatanIuranWarga
     * @throws ModelNotFoundException
     * @throws LogicException
     */
    public function validateContribution(int $id, int $userId): CatatanIuranWarga
    {
        return DB::transaction(function () use ($id, $userId) {
            $catatanIuran = CatatanIuranWarga::lockForUpdate()->findOrFail($id);

            if (!$catatanIuran->isPending()) {
                throw new LogicException("Hanya iuran dengan status PENDING yang dapat divalidasi.");
            }

            // Validasi keberadaan User Bendahara
            $user = User::where('user_id', $userId)->firstOrFail();

            $catatanIuran->update([
                'status' => PaymentStatus::APPROVED,
                'approved_by_user_id' => $user->user_id,
                'approved_at' => now(),
            ]);

            $catatanIuran->load(['kartuKeluarga', 'iuranType', 'approver']);

            DB::afterCommit(function () use ($catatanIuran) {
                event(new ContributionValidated($catatanIuran));
            });

            return $catatanIuran;
        });
    }

    /**
     * Membatalkan catatan iuran beserta memicu koreksi ledger.
     *
     * @param int $id
     * @param string $reason
     * @param int $userId
     * @return CatatanIuranWarga
     * @throws ModelNotFoundException
     * @throws LogicException
     * @throws InvalidArgumentException
     */
    public function invalidateContribution(int $id, string $reason, int $userId): CatatanIuranWarga
    {
        if (empty(trim($reason))) {
            throw new InvalidArgumentException("Alasan pembatalan wajib diisi.");
        }

        return DB::transaction(function () use ($id, $reason, $userId) {
            $catatanIuran = CatatanIuranWarga::lockForUpdate()->findOrFail($id);

            if (!$catatanIuran->isPending()) {
                throw new LogicException("Hanya iuran dengan status PENDING yang dapat dibatalkan.");
            }

            $user = User::where('user_id', $userId)->firstOrFail();

            // 1. Update status administrasi
            $catatanIuran->update([
                'status' => PaymentStatus::REJECTED,
                'rejection_notes' => trim($reason),
                'approved_by_user_id' => $user->user_id,
                'approved_at' => now(),
            ]);

            // 2. Delegasi Reversal Kas ke LedgerService
            $ledgerEntry = $catatanIuran->ledgerEntry()->first();
            if ($ledgerEntry) {
                $this->ledgerService->createAdjustment(
                    $ledgerEntry->transaction_id,
                    "Pembatalan catatan iuran: " . trim($reason),
                    $user->user_id
                );
            }

            // 3. Eager load
            $catatanIuran->load(['kartuKeluarga', 'iuranType', 'approver']);

            // 4. Dispatch Event
            DB::afterCommit(function () use ($catatanIuran) {
                event(new ContributionInvalidated($catatanIuran));
            });

            return $catatanIuran;
        });
    }

    /**
     * Validasi payload dasar (Domain Rules) sebelum menyentuh relasi.
     * 
     * @param array $data
     * @return void
     * @throws InvalidArgumentException
     */
    private function validateDomainRules(array $data): void
    {
        if (!isset($data['nominal']) || !is_numeric($data['nominal']) || $data['nominal'] <= 0) {
            throw new InvalidArgumentException("Nominal harus berupa angka lebih besar dari 0.");
        }

        if (!isset($data['periode_bulan']) || !is_numeric($data['periode_bulan']) || $data['periode_bulan'] < 1 || $data['periode_bulan'] > 12) {
            throw new InvalidArgumentException("Periode bulan tidak valid (1-12).");
        }

        if (!isset($data['periode_tahun']) || !is_numeric($data['periode_tahun']) || $data['periode_tahun'] < 2000) {
            throw new InvalidArgumentException("Periode tahun tidak valid.");
        }

        if (empty($data['tanggal_pembayaran'])) {
            throw new InvalidArgumentException("Tanggal pembayaran wajib diisi.");
        }

        try {
            $date = Carbon::parse($data['tanggal_pembayaran']);
            if ($date->isFuture()) {
                throw new InvalidArgumentException("Tanggal pembayaran tidak boleh di masa depan.");
            }
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Format tanggal pembayaran tidak valid.");
        }

        if (empty($data['no_kk'])) {
            throw new InvalidArgumentException("Nomor KK wajib diisi.");
        }

        if (empty($data['iuran_type_id'])) {
            throw new InvalidArgumentException("Jenis Iuran wajib diisi.");
        }
    }
}
