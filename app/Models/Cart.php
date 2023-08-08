<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Cart extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'carts';

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
        'user_id',
        'product_id',
        'size_id',
        'color_id',
        'quantity',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function sizes()
    {
        return $this->belongsTo(Size::class, 'size');
    }
    
    
}
