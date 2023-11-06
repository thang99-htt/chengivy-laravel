<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Returns extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'returns';

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
        'order_id',
        'requested_at',
        'returned_at',
        'reason',
        'description',
        'total_price',
        'status',
        'method',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function return_image()
    {
        return $this->hasMany(ReturnImage::class, 'return_id', 'id');
    }

    public function return_product()
    {
        return $this->hasMany(ReturnProduct::class, 'return_id');
    }


}
