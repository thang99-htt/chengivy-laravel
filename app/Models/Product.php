<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Type;
use App\Models\Images;
use App\Models\Size;

class Product extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'products';

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
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'type_id',
        'discount_percent',
        'status',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];
    
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function images() {
        return $this->hasMany(Images::class);
    }
    
    public function sizes() {
        return $this->belongsToMany(Size::class, 'product_size')->withPivot(['id', 'quantity', 'stock']);
    }

    public function product_size() {
        return $this->hasMany(ProductSize::class);
    }

    public static function getDiscountPrice($id) {
        $proDetails = Product::select('price', 'discount_percent')->where('id', $id)->first();
        $proDetails = json_decode(json_encode($proDetails), true);

        if($proDetails['discount_percent'] > 0) {
            $discounted_price = $proDetails['price'] - ($proDetails['price']*$proDetails['discount_percent']/100);
        } else {
            $discounted_price = 0;
        }

        return $discounted_price;
    }

    public function carts() {
        return $this->belongsToMany(Carts::class, 'carts_products')->withPivot(['id', 'quantity']);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }
}
