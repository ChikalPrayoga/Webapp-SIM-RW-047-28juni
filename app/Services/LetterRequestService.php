<?php

namespace App\Services;

use App\Models\PengajuanSurat;
use App\Models\LetterStatusHistory;
use App\Models\AnggotaKeluarga;
use App\Enums\LetterStatusEnum;
use Illuminate\Support\Facades\DB;

class LetterRequestService
{
    /**
     * Memproses pengajuan surat baru dari Warga
     *
     * @param array $data Data permohonan yang berisi nik, jenis_surat, dan keperluan.
     * @return PengajuanSurat
     * @throws \DomainException Jika NIK pemohon tidak ditemukan.
     */
    public function submitRequest(array $data): PengajuanSurat
    {
        // Validasi pemohon tersedia
        $pemohon = AnggotaKeluarga::where('nik', $data['nik'])->first();
        if (!$pemohon) {
            throw new \DomainException("Pemohon dengan NIK tersebut tidak ditemukan.");
        }

        $letter = DB::transaction(function () use ($data) {
            $letter = PengajuanSurat::create([
                'nik' => $data['nik'],
                'jenis_surat' => $data['jenis_surat'],
                'keperluan' => $data['keperluan'],
                'current_status' => LetterStatusEnum::SUBMITTED,
            ]);

            LetterStatusHistory::create([
                'pengajuan_id' => $letter->pengajuan_id,
                'actor_user_id' => null, // Warga tidak login
                'previous_status' => null,
                'new_status' => LetterStatusEnum::SUBMITTED,
                'notes' => 'Permohonan diajukan oleh warga.',
            ]);

            return $letter;
        });

        // Dispatch Event setelah transaction sukses
        event(new \App\Events\LetterSubmitted($letter));

        return $letter;
    }
}
