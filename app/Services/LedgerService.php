<?php

namespace App\Services;

use App\Models\FinancialTransaction;
use App\Enums\TransactionType;
use App\Enums\TransactionCategory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Events\FinancialTransactionCreated;
use App\Events\FinancialAdjustmentCreated;
use InvalidArgumentException;
use LogicException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LedgerService
{
    /**
     * Mencatat transaksi kas masuk (Income).
     *
     * @param array $data
     * @return FinancialTransaction
     * @throws InvalidArgumentException
     */
    public function createIncome(array $data): FinancialTransaction
    {
        $data['transaction_type'] = TransactionType::INCOME;
        return $this->createTransaction($data);
    }

    /**
     * Mencatat transaksi kas keluar (Expense).
     *
     * @param array $data
     * @return FinancialTransaction
     * @throws InvalidArgumentException
     */
    public function createExpense(array $data): FinancialTransaction
    {
        $data['transaction_type'] = TransactionType::EXPENSE;
        return $this->createTransaction($data);
    }

    /**
     * Internal method untuk memproses dan menyimpan transaksi kas secara aman.
     * Bertindak sebagai Single Entry Point bagi seluruh mutasi kas.
     *
     * @param array $data
     * @return FinancialTransaction
     * @throws InvalidArgumentException
     */
    protected function createTransaction(array $data): FinancialTransaction
    {
        // 1. Validasi Nominal (Amount)
        if (!isset($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
            throw new InvalidArgumentException("Nominal transaksi harus berupa angka dan lebih besar dari 0.");
        }

        // 2. Validasi Tipe Transaksi
        $type = $data['transaction_type'] ?? null;
        if (!$type instanceof TransactionType) {
            if (is_string($type)) {
                $type = TransactionType::tryFrom($type);
            }
        }
        if (!$type instanceof TransactionType) {
            throw new InvalidArgumentException("Tipe transaksi tidak valid.");
        }
        $data['transaction_type'] = $type;

        // 3. Validasi Kategori Transaksi
        $category = $data['category'] ?? null;
        if (!$category instanceof TransactionCategory) {
            if (is_string($category)) {
                $category = TransactionCategory::tryFrom($category);
            }
        }
        if (!$category instanceof TransactionCategory) {
            throw new InvalidArgumentException("Kategori transaksi tidak valid.");
        }
        $data['category'] = $category;

        // 4. Validasi Tanggal Transaksi (Tidak Boleh Di Masa Depan)
        if (empty($data['transaction_date'])) {
            throw new InvalidArgumentException("Tanggal transaksi wajib diisi.");
        }
        try {
            $date = Carbon::parse($data['transaction_date']);
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Format tanggal transaksi tidak valid.");
        }
        if ($date->isFuture()) {
            throw new InvalidArgumentException("Tanggal transaksi tidak boleh di masa depan.");
        }
        $data['transaction_date'] = $date->toDateString();

        // 5. Validasi Pembuat Transaksi (Creator)
        if (empty($data['created_by_user_id'])) {
            throw new InvalidArgumentException("Pembuat transaksi (creator) wajib ditentukan.");
        }
        if (!\App\Models\User::where('user_id', $data['created_by_user_id'])->exists()) {
            throw new InvalidArgumentException("User pembuat transaksi tidak ditemukan di sistem.");
        }

        // 6. Validasi Integritas Penyesuaian (Adjustment)
        if ($category === TransactionCategory::ADJUSTMENT) {
            if (empty($data['adjusted_transaction_id'])) {
                throw new InvalidArgumentException("Transaksi penyesuaian (adjustment) wajib menyertakan ID transaksi original.");
            }
            if (!FinancialTransaction::where('transaction_id', $data['adjusted_transaction_id'])->exists()) {
                throw new InvalidArgumentException("Transaksi original untuk penyesuaian tidak ditemukan.");
            }
        }

        // 7. Eksekusi Database Transaction & Sequence Generation
        return DB::transaction(function () use ($data, $date, $category) {
            if (!isset($data['transaction_number'])) {
                $data['transaction_number'] = $this->generateTransactionNumber($date);
            }

            $tx = FinancialTransaction::create($data);

            // Dispatch event standard transaction hanya jika bukan adjustment
            if ($category !== TransactionCategory::ADJUSTMENT) {
                DB::afterCommit(function() use ($tx) {
                    event(new FinancialTransactionCreated($tx));
                });
            }

            return $tx;
        });
    }

    /**
     * Melakukan pembatalan/koreksi (reversal) atas transaksi kas.
     * Menerapkan prinsip immutability dengan memposting mutasi lawan.
     *
     * @param int $transactionId
     * @param string $reason
     * @param int $userId
     * @return FinancialTransaction
     * @throws ModelNotFoundException
     * @throws LogicException
     * @throws InvalidArgumentException
     */
    public function createAdjustment(int $transactionId, string $reason, int $userId): FinancialTransaction
    {
        if (empty($transactionId)) {
            throw new InvalidArgumentException("ID transaksi original wajib ditentukan.");
        }

        if (empty(trim($reason))) {
            throw new InvalidArgumentException("Alasan koreksi (adjustment reason) wajib diisi.");
        }

        if (empty($userId)) {
            throw new InvalidArgumentException("User pengoreksi wajib ditentukan.");
        }

        return DB::transaction(function () use ($transactionId, $reason, $userId) {
            // Pessimistic Locking untuk mencegah double reversal konkuren
            $originalTx = FinancialTransaction::lockForUpdate()->find($transactionId);

            if (!$originalTx) {
                throw new ModelNotFoundException("Transaksi original tidak ditemukan.");
            }

            // Validasi: Koreksi ganda ditolak
            if ($originalTx->adjusted_transaction_id !== null) {
                throw new LogicException("Transaksi ini sudah pernah disesuaikan/dikoreksi.");
            }

            // Validasi: Koreksi atas koreksi ditolak
            if ($originalTx->category === TransactionCategory::ADJUSTMENT) {
                throw new LogicException("Transaksi adjustment tidak dapat disesuaikan kembali.");
            }

            // Validasi: Pengoreksi wajib terdaftar
            if (!\App\Models\User::where('user_id', $userId)->exists()) {
                throw new InvalidArgumentException("User pengoreksi tidak ditemukan di sistem.");
            }

            // Tentukan tipe reversal lawan secara otomatis
            $reversalType = $originalTx->isIncome() 
                ? TransactionType::EXPENSE 
                : TransactionType::INCOME;

            $reversalData = [
                'rt_code' => $originalTx->rt_code,
                'transaction_type' => $reversalType,
                'category' => TransactionCategory::ADJUSTMENT,
                'amount' => $originalTx->amount,
                'description' => "Koreksi untuk {$originalTx->transaction_number} - {$reason}",
                'transaction_date' => now()->toDateString(),
                'reference_type' => $originalTx->reference_type,
                'reference_id' => $originalTx->reference_id,
                'adjusted_transaction_id' => $originalTx->transaction_id,
                'created_by_user_id' => $userId,
            ];

            // Simpan transaksi penyeimbang baru
            $reversal = $this->createTransaction($reversalData);

            // Update metadata audit secara simetris pada transaksi asli
            $originalTx->update([
                'adjusted_transaction_id' => $reversal->transaction_id,
                'adjusted_by_user_id' => $userId,
                'adjusted_at' => now(),
            ]);

            // Dispatch event khusus penyesuaian pasca transaksi berhasil di-commit
            DB::afterCommit(function() use ($reversal) {
                event(new FinancialAdjustmentCreated($reversal));
            });

            return $reversal;
        });
    }

    /**
     * Menghasilkan nomor transaksi unik dan berurutan secara harian.
     * Menggunakan Pessimistic Locking untuk keamanan konkurensi tingkat tinggi.
     *
     * @param Carbon $date
     * @return string
     */
    protected function generateTransactionNumber(Carbon $date): string
    {
        $prefix = "TRX-{$date->format('Ymd')}-";
        
        $count = FinancialTransaction::whereDate('transaction_date', $date->toDateString())
                                     ->lockForUpdate()
                                     ->count();
        
        $sequence = str_pad((string)($count + 1), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $sequence;
    }

    /**
     * Menghitung total saldo kas secara dinamis (INCOME - EXPENSE).
     *
     * @param string|null $rtCode
     * @return float
     */
    public function getBalance(?string $rtCode = null): float
    {
        // Hanya menghitung mutasi kas aktif (yang belum disesuaikan/reversal)
        $query = FinancialTransaction::active();
        
        if ($rtCode !== null) {
            $query->rt($rtCode);
        } else {
            $query->rw();
        }
        
        $income = (float) (clone $query)->income()->sum('amount');
        $expense = (float) (clone $query)->expense()->sum('amount');
        
        return $income - $expense;
    }
}
