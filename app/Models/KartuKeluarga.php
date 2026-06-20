<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KartuKeluarga extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'no_kk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_kk',
        'rt_code',
        'alamat_lengkap',
        'blok',
        'nomor_rumah',
        'status_kepemilikan_rumah',
    ];

    public function anggotaKeluargas()
    {
        return $this->hasMany(AnggotaKeluarga::class, 'no_kk', 'no_kk');
    }
}
