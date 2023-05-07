<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Models\ImagesReview;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ReviewResource;

class ReviewsController extends Controller
{
    public function index()
    {
        $reviews = Review::with('user','product','images_review')->orderBy('created_at', 'DESC')->get();
        return response(ReviewResource::collection($reviews));
    }

    public function updateReviewStatus($id, Request $request) {
        $review = Review::find($id);
        $review->status = !$request->status;
        $review->save();
        
        return response()->json([
            'success' => true,
        ],200);
    }

}
