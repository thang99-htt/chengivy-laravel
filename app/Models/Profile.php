<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'profiles';

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
        'gender',
        'birth_date',
        'phone',
        'bank_account',
        'bank_number',
        'name'
    ];

    /**
     * Date time columns.
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
