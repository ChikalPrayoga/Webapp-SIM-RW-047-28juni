<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property int $iuran_id
 * @property string $no_kk
 * @property int $iuran_type_id
 * @property float $nominal
 * @property int $periode_bulan
 * @property int $periode_tahun
 * @property \Illuminate\Support\Carbon|null $tanggal_pembayaran
 * @property int|null $recorded_by_user_id
 * @property int|null $approved_by_user_id
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property PaymentStatus $status
 * @property string|null $payment_proof_path
 * @property string|null $rejection_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \App\Models\KartuKeluarga $kartuKeluarga
 * @property-read \App\Models\IuranType $iuranType
 * @property-read \App\Models\User|null $recorder
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\FinancialTransaction|null $ledgerEntry
 */
class CatatanIuranWarga extends Model
{
    use HasFactory;

    protected $primaryKey = 'iuran_id';

    protected $fillable = [
        'no_kk',
        'iuran_type_id',
        'nominal',
        'periode_bulan',
        'periode_tahun',
        'tanggal_pembayaran',
        'recorded_by_user_id',
        'approved_by_user_id',
        'approved_at',
        'status',
        'payment_proof_path',
        'rejection_notes',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_pembayaran' => 'date',
        'approved_at' => 'datetime',
        'status' => PaymentStatus::class,
    ];

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'no_kk', 'no_kk');
    }

    public function iuranType(): BelongsTo
    {
        return $this->belongsTo(IuranType::class, 'iuran_type_id', 'id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id', 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id', 'user_id');
    }

    public function ledgerEntry(): MorphOne
    {
        return $this->morphOne(FinancialTransaction::class, 'reference');
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === PaymentStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === PaymentStatus::REJECTED;
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', PaymentStatus::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', PaymentStatus::REJECTED);
    }

    public function scopeCurrentMonth($query)
    {
        return $query->where('periode_bulan', now()->month);
    }

    public function scopeCurrentYear($query)
    {
        return $query->where('periode_tahun', now()->year);
    }

    public function scopeByRt($query, string $rtCode)
    {
        return $query->whereHas('kartuKeluarga', function ($q) use ($rtCode) {
            $q->where('rt_code', $rtCode);
        });
    }

    public function scopeByContribution($query, IuranType $type)
    {
        return $query->where('iuran_type_id', $type->id);
    }

    public function scopeForKk($query, string $noKk)
    {
        return $query->where('no_kk', $noKk);
    }

    // Accessors

    public function getFormattedPeriodAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $monthName = $months[$this->periode_bulan] ?? $this->periode_bulan;
        return "{$monthName} {$this->periode_tahun}";
    }
}
