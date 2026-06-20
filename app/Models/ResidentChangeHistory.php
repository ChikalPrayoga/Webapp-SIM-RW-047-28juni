<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentChangeHistory extends Model
{
    use HasFactory;

    protected $primaryKey = 'history_id';

    protected $fillable = [
        'request_id',
        'actor_user_id',
        'previous_status',
        'new_status',
        'notes',
        'changed_at',
    ];

    public function request()
    {
        return $this->belongsTo(ResidentChangeRequest::class, 'request_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
