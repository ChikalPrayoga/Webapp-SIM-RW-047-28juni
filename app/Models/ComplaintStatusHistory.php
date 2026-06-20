<?php

namespace App\Models;

use App\Enums\ComplaintStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintStatusHistory extends Model
{
    use HasFactory;

    protected $primaryKey = 'history_id';

    protected $fillable = [
        'aspirasi_id',
        'actor_user_id',
        'previous_status',
        'new_status',
        'notes',
        'changed_at',
    ];

    protected $casts = [
        'previous_status' => ComplaintStatusEnum::class,
        'new_status' => ComplaintStatusEnum::class,
        'changed_at' => 'datetime',
    ];

    public function complaint()
    {
        return $this->belongsTo(LogLaporanAspirasi::class, 'aspirasi_id', 'aspirasi_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id', 'user_id');
    }
}
