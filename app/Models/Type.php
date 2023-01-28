<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'types';

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
        return $this->hasMany(Product::class, 'status_id');
    }
}
