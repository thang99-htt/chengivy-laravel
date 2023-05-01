<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'invoices';

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
        'date',
        'total_price',
    ];

    public function invoice_product()
    {
        return $this->hasMany(InvoiceProduct::class, 'invoice_id');
    }

}
