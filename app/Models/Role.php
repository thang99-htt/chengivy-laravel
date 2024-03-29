<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'roles';

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
        'description',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function staffs() {
        return $this->belongsToMany(Role::class, 'role_staff');
    }
    

    public function permissions() {
        return $this->belongsToMany(Permission::class);
    }

    public function permission_role()
    {
        return $this->hasMany(PermissionRole::class, 'role_id');
    }

    public function role_staff()
    {
        return $this->hasMany(RoleStaff::class, 'role_id');
    }
}
