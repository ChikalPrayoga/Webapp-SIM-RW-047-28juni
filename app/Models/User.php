<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'username',
        'full_name',
        'email',
        'password',
        'phone_number',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function position()
    {
        return $this->hasOne(OrganizationalPosition::class, 'user_id')->where('is_active', true);
    }

    public function assignmentsGiven()
    {
        return $this->hasMany(ComplaintAssignment::class, 'assigned_by_user_id', 'user_id');
    }

    public function assignmentsReceived()
    {
        return $this->hasMany(ComplaintAssignment::class, 'assigned_to_user_id', 'user_id');
    }

    public function hasPermissionTo($permissionName)
    {
        if (!$this->role) {
            return false;
        }

        if ($this->role->role_name === \App\Enums\RoleEnum::SUPER_ADMIN->value) {
            return true;
        }

        $permissionValue = $permissionName instanceof \App\Enums\PermissionEnum 
            ? $permissionName->value 
            : $permissionName;

        return $this->role->permissions()->where('permission_name', $permissionValue)->exists();
    }
}
