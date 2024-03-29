<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Permissions\HasPermissionsTrait;

use App\Models\Ward;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'level',
        'point',
        'actived'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
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


    public function profiles() {
        return $this->hasMany(Profile::class, 'user_id');
    }

    public function wards() {
        return $this->belongsToMany(Ward::class, 'contacts')->withPivot(['id', 'address', 'phone']);
    }
    
    public function delivery_address()
    {
        return $this->hasMany(DeliveryAddress::class, 'user_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function orders() {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function returns() {
        return $this->hasMany(Returns::class, 'user_id');
    }
}
