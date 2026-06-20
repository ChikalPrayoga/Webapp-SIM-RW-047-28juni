<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnggotaKeluarga extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nik',
        'no_kk',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'pekerjaan',
        'nomor_hp',
        'status_hubungan_keluarga',
        'status_sosio_ekonomi',
        'status_warga',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'no_kk', 'no_kk');
    }

    public function changeRequests()
    {
        return $this->hasMany(ResidentChangeRequest::class, 'nik', 'nik');
    }

    public function complaints()
    {
        return $this->hasMany(LogLaporanAspirasi::class, 'nik', 'nik');
    }
}
