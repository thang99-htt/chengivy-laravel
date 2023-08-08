<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'product_image';

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
        'color_id',
        'image',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}
