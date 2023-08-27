<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    
    public function toArray($request)
    {
        $colorGroups = $this->product->product_image
            ->groupBy('color_id')
            ->sortBy('color_id'); // Sắp xếp nhóm màu theo thứ tự color_id
    
        $firstColorGroup = $colorGroups->first(); // Lấy nhóm màu đầu tiên
    
        $firstColorImage = null;
    
        if ($firstColorGroup) {
            $firstColorImage = $firstColorGroup
                ->first() // Lấy hình ảnh đầu tiên trong nhóm màu đầu tiên
                ->image;
        }
        return [
            'id' => $this->id,
            'user' => $this->user,
            'date' => $this->date,
            'star' => $this->star,
            'fitted_value' => $this->fitted_value,
            'content' => $this->content,
            'status' => $this->status,
            'product' => $this->product,
            'image' => $firstColorImage,
            'classify' => $this->classify,
            'images' => $this->review_image,
            'reply' => $this->reply
        ];
    }
}
