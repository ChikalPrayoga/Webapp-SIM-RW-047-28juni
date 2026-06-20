<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentChangeRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'nik',
        'field_name',
        'old_value',
        'new_value',
        'current_status',
        'submitted_at',
    ];

    protected $casts = [
        'current_status' => \App\Enums\ResidentChangeStatusEnum::class,
    ];

    public function anggotaKeluarga()
    {
        return $this->belongsTo(AnggotaKeluarga::class, 'nik', 'nik');
    }

    public function histories()
    {
        return $this->hasMany(ResidentChangeHistory::class, 'request_id');
    }
}
