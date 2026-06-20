<?php

namespace App\Listeners;

use App\Events\ResidentChangeRequested;
use App\Models\AuditLog;
use App\Models\ActivityLog;

class RecordResidentChangeAuditLog
{
    public function __construct()
    {
        //
    }

    public function handle(ResidentChangeRequested $event): void
    {
        $request = $event->changeRequest;
        $user_id = auth()->id();

        // Create Audit Log
        AuditLog::create([
            'user_id' => $user_id,
            'entity_type' => 'ResidentChangeRequest',
            'entity_id' => $request->request_id,
            'action' => 'CREATED',
            'old_value' => null,
            'new_value' => $request->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'source' => 'WEB',
        ]);

        // Create Activity Log
        ActivityLog::create([
            'user_id' => $user_id,
            'activity_type' => 'CREATE_RESIDENT_CHANGE_REQUEST',
            'entity_type' => 'ResidentChangeRequest',
            'entity_id' => $request->request_id,
            'description' => "Pengajuan perubahan data untuk NIK: {$request->nik}, field: {$request->field_name}",
            'ip_address' => request()->ip(),
        ]);
    }
}
