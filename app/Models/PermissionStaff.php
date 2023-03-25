<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionStaff extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'permission_staff';

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
        'staff_id',
        'permission_id',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

}
