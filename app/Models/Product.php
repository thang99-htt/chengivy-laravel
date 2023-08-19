<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Brand;
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
        'brand_id',
        'name',
        'image',
        'price',
        'discount_percent',
        'price_final',
        'description',
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

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function product_image() {
        return $this->hasMany(ProductImage::class);
    }
    
    public function sizes() {
        return $this->belongsToMany(Size::class, 'inventories');
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
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
        return $this->belongsToMany(Cart::class, 'carts')->withPivot(['id', 'quantity']);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }
    
}
