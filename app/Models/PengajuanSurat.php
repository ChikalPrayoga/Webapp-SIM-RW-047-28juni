<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\LetterStatusEnum;
use App\Enums\LetterTypeEnum;

class PengajuanSurat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengajuan_surats';
    protected $primaryKey = 'pengajuan_id';

    protected $fillable = [
        'nik',
        'nomor_surat',
        'jenis_surat',
        'keperluan',
        'current_status',
        'tanggal_pengajuan',
        'tanggal_selesai',
    ];

    protected $casts = [
        'jenis_surat' => LetterTypeEnum::class,
        'current_status' => LetterStatusEnum::class,
        'tanggal_pengajuan' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function pemohon()
    {
        return $this->belongsTo(AnggotaKeluarga::class, 'nik', 'nik');
    }

    public function statusHistories()
    {
        return $this->hasMany(LetterStatusHistory::class, 'pengajuan_id', 'pengajuan_id')->orderBy('changed_at', 'desc');
    }
}
