<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagesReview extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'images_review';

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
        'review_id',
        'image',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];
    

}
