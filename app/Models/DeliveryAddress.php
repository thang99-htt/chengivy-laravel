<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property bigint unsigned $district_id district id
 * @property varchar $name name
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 * @property User $user belongsToed at
 * @property Ward $ward belongsTo
 * @property \Illuminate\Database\Eloquent\Collection $contact hasMany
   
 */

class DeliveryAddress extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'delivery_address';

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
        'name',
        'address',
        'phone',
        'default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Date time columns.
     */
    protected $dates = [];
    
    
}
