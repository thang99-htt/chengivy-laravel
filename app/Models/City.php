<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property varchar $name name
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 * @property timestamp $deleted_at deleted at
 * @property \Illuminate\Database\Eloquent\Collection $district hasMany
   
 */
class City extends Model
{
    use SoftDeletes;

    /**
     * Database table name
     */
    protected $table = 'cities';

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

    /**
     * districts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function districts()
    {
        return $this->hasMany(District::class, 'city_id');
    }

    public function wards()
    {
        return $this->hasManyThrough(Ward::class, District::class);
    }
}