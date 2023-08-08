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
    protected $table = 'payment_voucher';

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
        'supplier_id',
        'date',
        'total_price'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

}
