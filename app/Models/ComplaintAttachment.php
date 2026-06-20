<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintAttachment extends Model
{
    use HasFactory;

    protected $primaryKey = 'attachment_id';

    protected $fillable = [
        'aspirasi_id',
        'file_name',
        'file_path',
        'file_type',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function complaint()
    {
        return $this->belongsTo(LogLaporanAspirasi::class, 'aspirasi_id', 'aspirasi_id');
    }
}
