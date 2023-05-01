<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentVoucher extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'payment_vouchers';

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
        'date',
        'total_price',
        'description'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

}
