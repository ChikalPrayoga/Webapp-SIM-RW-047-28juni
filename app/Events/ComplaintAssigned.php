<?php

namespace App\Events;

use App\Models\ComplaintAssignment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintAssigned
{
    use Dispatchable, SerializesModels;

    public $assignment;

    public function __construct(ComplaintAssignment $assignment)
    {
        $this->assignment = $assignment;
    }
}
