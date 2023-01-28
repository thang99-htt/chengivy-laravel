<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Role;


class Admin extends Authenticatable
{
    use HasFactory;
    protected $guard = 'admin';

    protected $table = 'staffs';

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function isSuperAdmin()
    {
        if ($this->role_id == 1 && $this->status == 1) {
            return true;
        }
    }
}