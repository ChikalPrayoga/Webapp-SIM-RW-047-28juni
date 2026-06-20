<?php

namespace App\Services;

use App\Models\ComplaintAssignment;
use App\Models\LogLaporanAspirasi;
use App\Events\ComplaintAssigned;
use Illuminate\Support\Facades\DB;
use Exception;

class ComplaintAssignmentService
{
    public function assignComplaint(LogLaporanAspirasi $complaint, $assignedToUserId, $assignedByUserId, $notes = null): ComplaintAssignment
    {
        DB::beginTransaction();

        try {
            $assignment = ComplaintAssignment::create([
                'aspirasi_id' => $complaint->aspirasi_id,
                'assigned_by_user_id' => $assignedByUserId,
                'assigned_to_user_id' => $assignedToUserId,
                'notes' => $notes,
            ]);

            DB::commit();

            event(new ComplaintAssigned($assignment));

            return $assignment;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
