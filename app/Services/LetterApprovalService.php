<?php

namespace App\Services;

use App\Models\PengajuanSurat;
use App\Models\LetterStatusHistory;
use App\Models\User;
use App\Enums\LetterStatusEnum;
use Illuminate\Support\Facades\DB;

class LetterApprovalService
{
    /**
     * Helper internal untuk memusatkan logika update status dan histori
     */
    private function updateStatusAndRecordHistory(
        PengajuanSurat $letter,
        LetterStatusEnum $newStatus,
        User $actor,
        ?string $notes = null
    ): void {
        $previousStatus = $letter->current_status;

        $letter->current_status = $newStatus;
        if ($newStatus === LetterStatusEnum::COMPLETED) {
            $letter->tanggal_selesai = now();
        }
        $letter->save();

        LetterStatusHistory::create([
            'pengajuan_id' => $letter->pengajuan_id,
            'actor_user_id' => $actor->user_id,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
        ]);
    }

    /**
     * Dispatch event setelah berhasil transisi
     */
    private function dispatchUpdateEvent(PengajuanSurat $letter): void
    {
        event(new \App\Events\LetterStatusUpdated($letter));
    }

    public function process(PengajuanSurat $letter, User $actor, ?string $notes = null): PengajuanSurat
    {
        $processedLetter = DB::transaction(function () use ($letter, $actor, $notes) {
            $lockedLetter = PengajuanSurat::where('pengajuan_id', $letter->pengajuan_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedLetter->current_status !== LetterStatusEnum::SUBMITTED) {
                throw new \DomainException("Transisi status tidak valid. Aksi ini membutuhkan status SUBMITTED namun status saat ini adalah {$lockedLetter->current_status->value}.");
            }

            $this->updateStatusAndRecordHistory($lockedLetter, LetterStatusEnum::RT_REVIEW, $actor, $notes);

            return $lockedLetter;
        });

        $this->dispatchUpdateEvent($processedLetter);

        return $processedLetter;
    }

    public function forwardToRw(PengajuanSurat $letter, User $actor, ?string $notes = null): PengajuanSurat
    {
        $processedLetter = DB::transaction(function () use ($letter, $actor, $notes) {
            $lockedLetter = PengajuanSurat::where('pengajuan_id', $letter->pengajuan_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedLetter->current_status !== LetterStatusEnum::RT_REVIEW) {
                throw new \DomainException("Transisi status tidak valid. Aksi ini membutuhkan status RT_REVIEW namun status saat ini adalah {$lockedLetter->current_status->value}.");
            }

            if (!$lockedLetter->jenis_surat->requiresRwApproval()) {
                throw new \DomainException("Transisi status tidak valid. Jenis surat ini tidak memerlukan persetujuan Ketua RW.");
            }

            $this->updateStatusAndRecordHistory($lockedLetter, LetterStatusEnum::RW_REVIEW, $actor, $notes);

            return $lockedLetter;
        });

        $this->dispatchUpdateEvent($processedLetter);

        return $processedLetter;
    }

    public function complete(PengajuanSurat $letter, User $actor, ?string $nomorSurat = null, ?string $notes = null): PengajuanSurat
    {
        $processedLetter = DB::transaction(function () use ($letter, $actor, $nomorSurat, $notes) {
            $lockedLetter = PengajuanSurat::where('pengajuan_id', $letter->pengajuan_id)
                ->lockForUpdate()
                ->firstOrFail();

            $expectedStatus = $lockedLetter->jenis_surat->requiresRwApproval() 
                ? LetterStatusEnum::RW_REVIEW 
                : LetterStatusEnum::RT_REVIEW;

            if ($lockedLetter->current_status !== $expectedStatus) {
                throw new \DomainException("Transisi status tidak valid. Aksi ini membutuhkan status {$expectedStatus->value} namun status saat ini adalah {$lockedLetter->current_status->value}.");
            }

            if ($nomorSurat !== null) {
                $lockedLetter->nomor_surat = $nomorSurat;
            }

            $this->updateStatusAndRecordHistory($lockedLetter, LetterStatusEnum::COMPLETED, $actor, $notes);

            return $lockedLetter;
        });

        $this->dispatchUpdateEvent($processedLetter);

        return $processedLetter;
    }

    public function reject(PengajuanSurat $letter, User $actor, string $reason): PengajuanSurat
    {
        $processedLetter = DB::transaction(function () use ($letter, $actor, $reason) {
            $lockedLetter = PengajuanSurat::where('pengajuan_id', $letter->pengajuan_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($lockedLetter->current_status, [LetterStatusEnum::COMPLETED, LetterStatusEnum::REJECTED])) {
                throw new \DomainException("Transisi status tidak valid. Surat tidak bisa ditolak karena status saat ini adalah {$lockedLetter->current_status->value}.");
            }

            $this->updateStatusAndRecordHistory($lockedLetter, LetterStatusEnum::REJECTED, $actor, $reason);

            return $lockedLetter;
        });

        $this->dispatchUpdateEvent($processedLetter);

        return $processedLetter;
    }
}
