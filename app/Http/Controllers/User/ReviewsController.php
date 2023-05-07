<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Models\ImagesReview;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ReviewResource;

class ReviewsController extends Controller
{
    public function store(Request $request)
    {
        $reviews = $request['review'];
$t=[];
        foreach ($reviews as $reviewData) {
            $review = new Review();
            $review->user_id = $reviewData['user_id'];
            $review->product_id = $reviewData['product_id'];
            $review->content = $reviewData['content'];
            $review->rate = $reviewData['rate'];
            $review->save();

            $reviewId = $review->id;
            if($reviewData['images']) {
                foreach($reviewData['images'] as $image) {
                    // $strpos = strpos($image, ';');
                    // $sub = substr($image, 0, $strpos);
                    // $ex = explode("/", $sub)[1];
                    // $imageName = time().".".$ex;
                    // $images = Image::make($image);
                    // $upload_path = public_path()."/storage/uploads/reviews/";
                    // Image::make($image)->save(public_path()."/storage/uploads/reviews/".$image);

                    $imageReview = new ImagesReview();
                    $imageReview->review_id = $reviewId; 
                    $imageReview->image = $image;
                    $imageReview->save();
                    $t[]= $image;
                }            
            }

        }

        return response()->json($t, 200); 
                
    }

}
