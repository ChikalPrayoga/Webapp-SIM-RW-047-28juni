<?php

namespace App\Listeners;

use App\Events\ComplaintSubmitted;
use App\Events\ComplaintAssigned;
use App\Models\ActivityLog;

class RecordComplaintActivity
{
    public function handle($event): void
    {
        if ($event instanceof ComplaintSubmitted) {
            ActivityLog::create([
                'user_id' => null, // Citizen without user account
                'activity_type' => 'CREATE_COMPLAINT',
                'entity_type' => get_class($event->complaint),
                'entity_id' => $event->complaint->aspirasi_id,
                'description' => 'Complaint submitted with ID: ' . $event->complaint->aspirasi_id,
                'ip_address' => request()->ip(),
            ]);
        } elseif ($event instanceof ComplaintAssigned) {
            ActivityLog::create([
                'user_id' => $event->assignment->assigned_by_user_id,
                'activity_type' => 'ASSIGN_COMPLAINT',
                'entity_type' => get_class($event->assignment->complaint),
                'entity_id' => $event->assignment->aspirasi_id,
                'description' => 'Complaint assigned to User ID: ' . $event->assignment->assigned_to_user_id,
                'ip_address' => request()->ip(),
            ]);
        }
    }
}
