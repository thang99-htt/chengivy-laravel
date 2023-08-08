<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property bigint unsigned $city_id city id
 * @property varchar $name name
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 * @property City $city belongsTo
 * @property \Illuminate\Database\Eloquent\Collection $ward hasMany
   
 */
class District extends Model
{

    use SoftDeletes;

    /**
     * Database table name
     */
    protected $table = 'districts';

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
        'city_id',
        'name'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    /**
     * city
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * wards
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wards()
    {
        return $this->hasMany(Ward::class, 'district_id');
    }
}