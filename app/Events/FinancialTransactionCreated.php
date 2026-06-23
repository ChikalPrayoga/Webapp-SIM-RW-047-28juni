<?php

namespace App\Events;

use App\Models\FinancialTransaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FinancialTransactionCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var FinancialTransaction
     */
    public $transaction;

    /**
     * Create a new event instance.
     *
     * @param FinancialTransaction $transaction
     */
    public function __construct(FinancialTransaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
