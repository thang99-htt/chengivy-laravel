<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPImportCouponProductroduct extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'import_coupon_product';

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
        'import_coupon_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function import_coupon()
    {
        return $this->belongsTo(ImportCoupon::class, 'import_coupon_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
