<?php

namespace App\Services;

use App\Models\ResidentChangeRequest;
use App\Models\ResidentChangeHistory;
use App\Models\AnggotaKeluarga;
use App\Models\AuditLog;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class ResidentChangeService
{
    public function approveRequest(ResidentChangeRequest $request, $notes = null)
    {
        return DB::transaction(function () use ($request, $notes) {
            $warga = AnggotaKeluarga::where('nik', $request->nik)->firstOrFail();
            $oldValue = $warga->toArray();

            // Update main data
            $warga->update([
                $request->field_name => $request->new_value
            ]);

            // Update request status
            $request->update(['current_status' => \App\Enums\ResidentChangeStatusEnum::APPROVED->value]);

            // Create history
            $userId = auth()->id();
            ResidentChangeHistory::create([
                'request_id' => $request->request_id,
                'actor_user_id' => $userId,
                'previous_status' => \App\Enums\ResidentChangeStatusEnum::PENDING->value,
                'new_status' => \App\Enums\ResidentChangeStatusEnum::APPROVED->value,
                'notes' => $notes,
            ]);

            // Audit Log for Warga
            AuditLog::create([
                'user_id' => $userId,
                'entity_type' => 'AnggotaKeluarga',
                'entity_id' => $warga->nik,
                'action' => 'UPDATED_VIA_APPROVAL',
                'old_value' => $oldValue,
                'new_value' => $warga->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            ActivityLog::create([
                'user_id' => $userId,
                'activity_type' => 'APPROVE_RESIDENT_CHANGE',
                'entity_type' => 'ResidentChangeRequest',
                'entity_id' => $request->request_id,
                'description' => "Menyetujui perubahan data NIK: {$request->nik}, field: {$request->field_name}",
                'ip_address' => request()->ip(),
            ]);

            return true;
        });
    }

    public function rejectRequest(ResidentChangeRequest $request, $notes)
    {
        return DB::transaction(function () use ($request, $notes) {
            $request->update(['current_status' => \App\Enums\ResidentChangeStatusEnum::REJECTED->value]);

            $userId = auth()->id();
            ResidentChangeHistory::create([
                'request_id' => $request->request_id,
                'actor_user_id' => $userId,
                'previous_status' => \App\Enums\ResidentChangeStatusEnum::PENDING->value,
                'new_status' => \App\Enums\ResidentChangeStatusEnum::REJECTED->value,
                'notes' => $notes,
            ]);

            ActivityLog::create([
                'user_id' => $userId,
                'activity_type' => 'REJECT_RESIDENT_CHANGE',
                'entity_type' => 'ResidentChangeRequest',
                'entity_id' => $request->request_id,
                'description' => "Menolak perubahan data NIK: {$request->nik}, field: {$request->field_name}",
                'ip_address' => request()->ip(),
            ]);

            return true;
        });
    }
}
