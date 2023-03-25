<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleStaff extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'role_staff';

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
        'role_id',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
