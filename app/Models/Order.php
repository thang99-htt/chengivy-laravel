<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'orders';

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
        'staff_id',
        'user_id',
        'status_id',
        'payment_method_id',
        'voucher_id',
        'ordered_at',
        'confirmed_at',
        'estimated_at',
        'cancled_at',
        'receipted_at',
        'total_value',
        'fee',
        'total_discount',
        'total_price',
        'paid',
        'note',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];
    
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function order_product()
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    
    // with id = ward_id
    public static function getAddressDetail($id) {
        $ward = Ward::find($id);
        $district = $ward->district->name;
        $city = $ward->district->city->name;

        return $ward;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
