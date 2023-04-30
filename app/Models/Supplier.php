<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'supplier';

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
        'address',
        'phone',
        'email'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function import_coupon() {
        return $this->hasMany(ImportCoupon::class, 'import_coupon_id');
    }
}
