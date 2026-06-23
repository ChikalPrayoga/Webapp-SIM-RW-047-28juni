<?php

namespace App\Models;

use App\Enums\ContributionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $default_nominal
 * @property ContributionType $type
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CatatanIuranWarga[] $payments
 */
class IuranType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'default_nominal',
        'type',
        'is_active',
    ];

    protected $casts = [
        'default_nominal' => 'decimal:2',
        'type' => ContributionType::class,
        'is_active' => 'boolean',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(CatatanIuranWarga::class, 'iuran_type_id', 'id');
    }

    /**
     * Scope a query to only include active Iuran types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the Iuran type is active.
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }
}
