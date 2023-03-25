<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'permission_role';

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
        'permisison_id',
        'role_id',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

}
