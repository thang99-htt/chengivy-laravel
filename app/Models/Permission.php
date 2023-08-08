<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'permissions';

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
        return $this->belongsToMany(Staff::class, 'permission_staff');
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'permission_role');
    }

    public function childs() {
        return $this->hasMany(Permission::class, 'group_id');
    }

    public static function permissions() {
        $getPermissions = Permission::with(['childs'])->where(['group_id' => 0])->get()->toArray();
        return $getPermissions;
    }
}
