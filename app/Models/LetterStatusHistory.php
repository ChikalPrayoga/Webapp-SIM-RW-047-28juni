<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\LetterStatusEnum;

class LetterStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'letter_status_histories';
    protected $primaryKey = 'history_id';
    
    // We disable standard updated_at since this is a history table
    const UPDATED_AT = null;

    protected $fillable = [
        'pengajuan_id',
        'actor_user_id',
        'previous_status',
        'new_status',
        'notes',
        'changed_at',
    ];

    protected $casts = [
        'previous_status' => LetterStatusEnum::class,
        'new_status' => LetterStatusEnum::class,
        'changed_at' => 'datetime',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanSurat::class, 'pengajuan_id', 'pengajuan_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id', 'user_id');
    }
}
