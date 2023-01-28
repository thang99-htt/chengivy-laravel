<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Size extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'sizes';

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
        'name',
        'description',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];
    
    public function products() {
        return $this->belongsToMany(Product::class, 'product_size')->withPivot(['id', 'quantity']);
    }
}
