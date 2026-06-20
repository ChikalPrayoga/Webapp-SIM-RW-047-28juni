<?php

namespace App\Models;

use App\Enums\ComplaintStatusEnum;
use App\Enums\ComplaintCategoryEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogLaporanAspirasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_laporan_aspirasis';
    protected $primaryKey = 'aspirasi_id';

    protected $fillable = [
        'nik',
        'kanal_laporan',
        'teks_keluhan',
        'ai_category',
        'ai_priority',
        'ai_summary',
        'ai_confidence',
        'current_status',
        'submitted_at',
        'resolved_at',
    ];

    protected $casts = [
        'current_status' => ComplaintStatusEnum::class,
        'ai_category' => ComplaintCategoryEnum::class,
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function pelapor()
    {
        return $this->belongsTo(AnggotaKeluarga::class, 'nik', 'nik');
    }

    public function assignments()
    {
        return $this->hasMany(ComplaintAssignment::class, 'aspirasi_id', 'aspirasi_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(ComplaintStatusHistory::class, 'aspirasi_id', 'aspirasi_id');
    }

    public function attachments()
    {
        return $this->hasMany(ComplaintAttachment::class, 'aspirasi_id', 'aspirasi_id');
    }
}
