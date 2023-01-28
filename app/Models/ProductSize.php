<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'product_size';

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
        'product_id',
        'size_id',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public static function getProductQuantity($product_id, $size_id) {
        $getProductQuantity = ProductSize::select('quantity')->where(['product_id'=>$product_id, 'size_id'=>$size_id])->first();
        return $getProductQuantity->quantity;
    }
}
