<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnImage extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'return_image';

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
        'return_id',
        'image',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];
    

    public function returns()
    {
        return $this->belongsTo(Returns::class, 'return_id', 'id');
    }

}
