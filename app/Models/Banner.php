<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    public static function banners() {
        $getBanners = Banner::get()->toArray();
        return $getBanners;
    }
}
