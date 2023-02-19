<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Product extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'products';

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
        'price',
        'start_date',
        'end_date',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
