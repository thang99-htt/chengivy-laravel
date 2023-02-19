<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property bigint unsigned $district_id district id
 * @property varchar $name name
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 * @property timestamp $deleted_at delet
 * @property User $user belongsToed at
 * @property Ward $ward belongsTo
 * @property \Illuminate\Database\Eloquent\Collection $contact hasMany
   
 */

class Contact extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'contacts';

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
        'user_id',
        'ward_id',
    ];

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Date time columns.
     */
    protected $dates = [];
    
    
}
