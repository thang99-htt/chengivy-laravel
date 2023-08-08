<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $groupedImages = $this->groupImagesByColor();
        $firstImage = null;

        if (!empty($groupedImages) && isset($groupedImages[0]['images'][0]['image'])) {
            $firstImage = $groupedImages[0]['images'][0]['image'];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'price_final' => $this->price_final,
            'discount_percent' => $this->discount_percent,
            'image' => $firstImage,
            'category' => $this->category->name,
            'category_parent' => $this->category->parent ? $this->category->parent->name : null,
            'category_url' => $this->category->url,
            'brand' => $this->brand->name,
            'status' => $this->status,
            'images' => $this->product_image
                ->groupBy('color_id')
                ->map(function ($groupedItems, $color) {
                return [
                    'color_id' => $color,
                    'color_name' => $groupedItems->first()->color->name,
                    'color' => $groupedItems->first()->color->color,
                    'items' => $groupedItems->map(function ($item) {
                        return [
                            'image' => $item->image
                        ];
                    }),
                ];
            }),
            'inventories' => $this->inventories
                ->groupBy('month_year')
                ->map(function ($groupedItems, $monthYear) {
                return [
                    'month_year' => $monthYear,
                    'items' => $groupedItems->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'size_id' => $item->size_id,
                            'size_name' => $item->size->name,
                            'color_id' => $item->color_id,
                            'color_name' => $item->color->name,
                            'total_initial' => $item->total_initial,
                            'total_import' => $item->total_import,
                            'total_export' => $item->total_export,
                            'total_final' => $item->total_final,
                        ];
                    }),
                ];
            }),
            'reviews' => $this->reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'user' => $review->user->name,
                    'content' => $review->content,
                    'rate' => $review->rate,
                    'status' => $review->status,
                    'created_at' => $review->created_at,
                    'images_review' => $review->images_review->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image' => $image->image
                        ];
                    }),
                ];
            }), 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            
        ];
    }

    public function groupImagesByColor()
    {
        $groupedImages = [];
        foreach ($this->product_image as $image) {
            $colorId = $image['color_id'];
            if (!isset($groupedImages[$colorId])) {
                $groupedImages[$colorId] = [
                    'color' => $image['color'],
                    'images' => [],
                ];
            }
            $groupedImages[$colorId]['images'][] = [
                'id' => $image['id'],
                'image' => $image['image'],
            ];
        }
        return array_values($groupedImages);
    }

}
