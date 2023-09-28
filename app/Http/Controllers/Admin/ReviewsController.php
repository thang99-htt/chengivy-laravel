<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ReviewResource;
use App\Jobs\UploadReviewToGoogleDrive;
use Carbon\Carbon;

class ReviewsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->input('startDate'))->startOfDay();
        $endDate = Carbon::createFromFormat('d/m/Y', $request->input('endDate'))->endOfDay();

        $reviews = Review::with('user','product','review_image')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('created_at', 'DESC')->get();
        return response(ReviewResource::collection($reviews));
    }

    public function show($id)
    {
        $review = Review::with('user','product','review_image')->find($id);
        return response()->json(new ReviewResource($review));
    }

    public function store(Request $request)
    {
        foreach($request->all() as $item) {
            $review = new Review;
            $review->user_id = $item['user_id'];
            $review->product_id = $item['product_id'];
            $review->classify = $item['classify'];
            $review->star = $item['star'];
            $review->fitted_value = $item['fitted_value'];
            $review->content = $item['content'];
            $review->status = 1;
            $review->date = Carbon::now('Asia/Ho_Chi_Minh');
            $review->save();
            
            $reviewId = $review->id;
            $imagesData = $item['images'];

            if ($imagesData) {
                foreach ($imagesData as $data) {
                    $base64Image = $data['image'];
                    UploadReviewToGoogleDrive::dispatch($reviewId, $base64Image);
                }
            }
    
        }

        return response()->json([
            'success' => 'success',
            'message' => 'Cảm ơn bạn đã đánh giá!',
        ]);

        // return response()->json($request->all());
    }

    public function updateReviewStatus($id, Request $request) {
        $review = Review::find($request->id);
        if($request->status == 0) {
            $review->status = 1;
        } else {
            $review->status = 0;
        }
        $review->save();
        
        return response()->json([
            'success' => true,
        ],200);
    }

    public function update($id, Request $request)
    {
        $review = Review::find($id);
        $review->reply = $request->reply;
        $review->save();

        return response()->json([
            'success' => true,
        ],200);
    }

    public function hiddenIds(Request $request)
    {
        $selectedIds = $request->all(); // Lấy danh sách selectedIds từ request
        $reviews = Review::whereIn('id', $selectedIds['data'])->get(); // Sử dụng whereIn để lấy các bản ghi tương ứng với selectedIds
        foreach($reviews as $review) {
            if($review->status == 0) {
                $review->status = 1;
            } else {
                $review->status = 0;
            }
            $review->save();
        }      
        return response()->json(['success'=>$reviews], 200);
    }

}
