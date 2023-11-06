<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Permissions\HasPermissionsTrait;

use App\Models\Ward;

class Staff extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Database table name
     */
    protected $table = 'staffs';

    /**
     * Use timestamps 
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'name',
        'identity_card',
        'email',
        'phone',
        'gender',
        'birth_date',
        'address',
        'image',
        'status',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];
    
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
    ];

    public function roles() {
        return $this->belongsToMany(Role::class, 'role_staff');
    }

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'permission_staff');
    }

    public function role_staff() {
        return $this->hasMany(RoleStaff::class);
    }

    public function permission_staff() {
        return $this->hasMany(PermissionStaff::class);
    }

    public function permission_role() {
        return $this->hasMany(PermissionRole::class);
    }

    public function orders() {
        return $this->hasMany(Order::class, 'staff_delivery_id');
    }
}
