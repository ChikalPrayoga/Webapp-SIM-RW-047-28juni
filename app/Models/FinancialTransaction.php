<?php

namespace App\Models;

use App\Enums\TransactionCategory;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $transaction_id
 * @property string $transaction_number
 * @property string|null $rt_code
 * @property TransactionType $transaction_type
 * @property TransactionCategory $category
 * @property float $amount
 * @property string $description
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property int|null $adjusted_transaction_id
 * @property int|null $adjusted_by_user_id
 * @property \Illuminate\Support\Carbon|null $adjusted_at
 * @property int $created_by_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Model|null $reference
 * @property-read \App\Models\User $creator
 * @property-read \App\Models\User|null $adjuster
 * @property-read FinancialTransaction|null $originalTransaction
 * @property-read FinancialTransaction|null $reversalTransaction
 * @property-read string $formatted_amount
 */
class FinancialTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'transaction_number',
        'rt_code',
        'transaction_type',
        'category',
        'amount',
        'description',
        'transaction_date',
        'reference_type',
        'reference_id',
        'adjusted_transaction_id',
        'adjusted_by_user_id',
        'adjusted_at',
        'created_by_user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_type' => TransactionType::class,
        'category' => TransactionCategory::class,
        'transaction_date' => 'date',
        'adjusted_at' => 'datetime',
    ];

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'user_id');
    }

    public function adjuster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by_user_id', 'user_id');
    }

    public function originalTransaction(): BelongsTo
    {
        return $this->belongsTo(FinancialTransaction::class, 'adjusted_transaction_id', 'transaction_id');
    }

    public function reversalTransaction(): HasOne
    {
        return $this->hasOne(FinancialTransaction::class, 'adjusted_transaction_id', 'transaction_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedTransactionNumberAttribute(): string
    {
        return '#' . $this->transaction_number;
    }

    // Helpers

    public function isIncome(): bool
    {
        return $this->transaction_type === TransactionType::INCOME;
    }

    public function isExpense(): bool
    {
        return $this->transaction_type === TransactionType::EXPENSE;
    }

    public function isAdjustment(): bool
    {
        return $this->category === TransactionCategory::ADJUSTMENT;
    }

    public function isActive(): bool
    {
        return $this->adjusted_transaction_id === null;
    }

    // Scopes

    public function scopeIncome($query)
    {
        return $query->where('transaction_type', TransactionType::INCOME);
    }

    public function scopeExpense($query)
    {
        return $query->where('transaction_type', TransactionType::EXPENSE);
    }

    public function scopeAdjustment($query)
    {
        return $query->where('category', TransactionCategory::ADJUSTMENT);
    }

    public function scopeRw($query)
    {
        return $query->whereNull('rt_code');
    }

    public function scopeRt($query, string $rtCode)
    {
        return $query->where('rt_code', $rtCode);
    }

    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('transaction_date', now()->month)
                     ->whereYear('transaction_date', now()->year);
    }

    public function scopeCurrentYear($query)
    {
        return $query->whereYear('transaction_date', now()->year);
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('transaction_date', [$start, $end]);
    }

    public function scopeByCategory($query, TransactionCategory $category)
    {
        return $query->where('category', $category);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('adjusted_transaction_id');
    }
}
