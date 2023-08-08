<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'inventories';

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
        'month_year',
        'product_id',
        'size_id',
        'color_id',
        'total_initial',
        'total_import',
        'total_export',
        'total_final'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    // public static function getProductQuantity($product_id, $size_id) {
    //     $getProductQuantity = Inventory::select('quantity', 'stock')->where(['product_id'=>$product_id, 'size_id'=>$size_id])->first();
    //     return $getProductQuantity->stock;
    // }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}
