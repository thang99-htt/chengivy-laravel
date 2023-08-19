<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReceivedDocketProductDetail extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'stock_received_docket_product_detail';

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
        'stock_received_docket_product_id',
        'product_id',
        'color_id',
        'size_id',
        'quantity',
    ];

    public function stock_received_docket_product()
    {
        return $this->belongsTo(StockReceivedDocketProduct::class, 'stock_received_docket_product_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
}
