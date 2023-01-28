<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property varchar $name name
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 * @property timestamp $deleted_at deleted at
 * @property \Illuminate\Database\Eloquent\Collection $district hasMany
   
 */
class Employee extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'employees';

    /**
     * Use timestamps 
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    
}