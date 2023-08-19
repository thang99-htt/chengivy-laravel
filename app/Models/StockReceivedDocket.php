<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReceivedDocket extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'stock_received_docket';

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
        'form',
        'date',
        'total_price',
        'value_added',
        'total_value',
        'image',
        'note'
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

    public function stock_received_docket_product()
    {
        return $this->hasMany(StockReceivedDocketProduct::class, 'stock_received_docket_id');
    }

    public function stock_received_docket_product_detail()
    {
        return $this->hasMany(StockReceivedDocketProductDetail::class, 'stock_received_docket_id');
    }
}
