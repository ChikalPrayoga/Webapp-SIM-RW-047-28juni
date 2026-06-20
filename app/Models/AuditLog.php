<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'audit_id';

    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'action',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
        'source',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
