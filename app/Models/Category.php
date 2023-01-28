<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Database table name
     */
    protected $table = 'categories';

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
        'parent_id',
        'name',
        'image',
        'description',
        'url',
        'status'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];
    
    public function parent() {
        return $this->belongsTo(Category::class, 'parent_id')->select('id','name');
    }

    public function childs() {
        return $this->hasMany(Category::class, 'parent_id')->where('status',1);
    }

    public static function categories() {
        $getCategories = Category::with(['childs'])->where(['parent_id' => 0, 'status' => 1])->get()->toArray();
        return $getCategories;
    }

    public function products() {
        return $this->hasMany(Product::class, 'category_id');
    }

    public static function categoryDetails($url) {
        $categoryDetails = Category::select('id', 'name')->with('childs')->where(['url' => $url, 'status' => 1])->first()->toArray();
        
        $catIds = array();
        $catIds[] = $categoryDetails['id'];
        foreach($categoryDetails['childs'] as $key => $child) {
            $catIds[] = $child['id'];
        }
        
        $resp = array('catIds' => $catIds, 'categoryDetails' => $categoryDetails);
        return $resp;
    }

    public static function categoriesDetails($url) {
        $categoriesDetails = Category::all();
        
        $resp = array('categoriesDetails' => $categoriesDetails);
        return $resp;
    }
}
