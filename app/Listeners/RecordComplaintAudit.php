<?php

namespace App\Listeners;

use App\Events\ComplaintStatusUpdated;
use App\Models\AuditLog;
use App\Enums\AuditLogSourceEnum;

class RecordComplaintAudit
{
    public function handle(ComplaintStatusUpdated $event): void
    {
        // Only record audit if the status was changed by an admin (not just initial submission)
        if ($event->actor) {
            AuditLog::create([
                'user_id' => $event->actor->user_id,
                'entity_type' => get_class($event->complaint),
                'entity_id' => $event->complaint->aspirasi_id,
                'action' => 'UPDATE_COMPLAINT_STATUS',
                'old_value' => ['status' => $event->oldStatus],
                'new_value' => ['status' => $event->newStatus],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'System',
                'source' => AuditLogSourceEnum::WEB->value,
            ]);
        }
    }
}
