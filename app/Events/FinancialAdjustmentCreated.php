<?php

namespace App\Events;

use App\Models\FinancialTransaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FinancialAdjustmentCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var FinancialTransaction
     */
    public $adjustment;

    /**
     * Create a new event instance.
     *
     * @param FinancialTransaction $adjustment
     */
    public function __construct(FinancialTransaction $adjustment)
    {
        $this->adjustment = $adjustment;
    }
}
