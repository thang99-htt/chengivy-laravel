<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Category;
use Intervention\Image\Facades\Image;

class UploadImageController extends Controller
{
    public function store(Request $request)
    {
        $image = $request->all()[0];
        $strpos = strpos($image, ';');
        $sub = substr($image, 0, $strpos);
        $ex = explode("/", $sub)[1];
        $imageName = time().".".$ex;
        $img = Image::make($image);
        $upload_path = public_path()."/storage/uploads/search/";
        $img->save($upload_path.$imageName);
        $url = "http://localhost:8000/storage/uploads/search/".$imageName;
        //xóa hình sau 2 phút
        return response()->json($url);
    }

}
