<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReceivedDocketProduct extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'stock_received_docket_product';

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
        'stock_received_docket_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function stock_received_docket_product_detail()
    {
        return $this->hasMany(StockReceivedDocketProductDetail::class, 'stock_received_docket_product_id');
    }

    public function stock_received_docket()
    {
        return $this->belongsTo(StockReceivedDocket::class, 'stock_received_docket_id');
    }
}
