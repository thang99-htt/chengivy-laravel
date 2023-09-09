<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Notification extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'notifications';

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
        'user',
        'message',
        'type',
        'link',
        'status',
        'date'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    
    
}
