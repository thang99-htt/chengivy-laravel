<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'review_image';

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
