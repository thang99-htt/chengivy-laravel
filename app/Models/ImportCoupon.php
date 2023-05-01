<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportCoupon extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'import_coupon';

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
        'payment_voucher_id',
        'date',
        'total_price',
        'value_added',
        'total_value',
        'image',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];
    
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function payment_voucher()
    {
        return $this->belongsTo(PaymentVoucher::class, 'payment_voucher_id');
    }

    public function import_coupon_product()
    {
        return $this->hasMany(ImportCouponProduct::class, 'import_coupon_id');
    }
}
