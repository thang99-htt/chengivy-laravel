<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

/**
 * @property bigint unsigned $district_id district id
 * @property varchar $name name
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 * @property timestamp $deleted_at deleted at
 * @property District $district belongsTo
 * @property \Illuminate\Database\Eloquent\Collection $contact hasMany
   
 */
class Ward extends Model
{

    use SoftDeletes;
    /**
     * Database table name
     */
    protected $table = 'wards';

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
        'district_id',
        'name'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    /**
     * district
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * contacts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function users() {
        return $this->belongsToMany(User::class, 'contacts')->withPivot(['id' ,'address', 'phone', 'default']);
    }
}