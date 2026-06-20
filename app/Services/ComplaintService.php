<?php

namespace App\Services;

use App\Models\LogLaporanAspirasi;
use App\Models\ComplaintAttachment;
use App\Events\ComplaintSubmitted;
use App\Events\ComplaintStatusUpdated;
use App\Enums\ComplaintStatusEnum;
use App\Enums\ComplaintCategoryEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class ComplaintService
{
    public function submitComplaint(array $data, array $files = []): LogLaporanAspirasi
    {
        DB::beginTransaction();

        try {
            $complaint = LogLaporanAspirasi::create([
                'nik' => $data['nik'],
                'kanal_laporan' => $data['kanal_laporan'] ?? 'WEB',
                'teks_keluhan' => $data['teks_keluhan'],
                'current_status' => ComplaintStatusEnum::SUBMITTED->value,
            ]);

            // Handle Attachments
            foreach ($files as $file) {
                // Ensure it's stored securely and not publicly accessible by default
                $path = $file->storeAs(
                    'complaints', 
                    date('Ymd_His') . '_' . $data['nik'] . '_' . \Str::random(10) . '.' . $file->extension(),
                    'local'
                );
                
                ComplaintAttachment::create([
                    'aspirasi_id' => $complaint->aspirasi_id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                ]);
            }

            DB::commit();

            // Dispatch event to trigger Audit/Activity Logs and future AI Job
            event(new ComplaintSubmitted($complaint));

            return $complaint;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateStatus(LogLaporanAspirasi $complaint, ComplaintStatusEnum $newStatus, $actor = null, string $notes = null, ComplaintCategoryEnum $category = null, string $priority = null)
    {
        DB::beginTransaction();

        try {
            $oldStatus = $complaint->current_status->value ?? $complaint->current_status;

            $complaint->current_status = $newStatus->value;
            
            if ($category) {
                $complaint->ai_category = $category->value;
            }
            if ($priority) {
                $complaint->ai_priority = $priority;
            }
            if ($newStatus === ComplaintStatusEnum::RESOLVED || $newStatus === ComplaintStatusEnum::CLOSED) {
                $complaint->resolved_at = now();
            }

            $complaint->save();

            DB::commit();

            event(new ComplaintStatusUpdated($complaint, $oldStatus, $newStatus->value, $actor, $notes));

            return $complaint;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
