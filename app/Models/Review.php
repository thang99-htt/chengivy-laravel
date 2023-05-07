<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Review extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'reviews';

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
        'content',
        'rate',
        'status'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function images_review() {
        return $this->hasMany(ImagesReview::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
