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
        $colorGroups = $this->product_image
            ->groupBy('color_id')
            ->sortBy('color_id'); // Sắp xếp nhóm màu theo thứ tự color_id

        $firstColorGroup = $colorGroups->first(); // Lấy nhóm màu đầu tiên

        $firstColorImage = null;

        if ($firstColorGroup) {
            $firstColorImage = $firstColorGroup
                ->first() // Lấy hình ảnh đầu tiên trong nhóm màu đầu tiên
                ->image;
        }

        $lastStockReceivedDocket = collect($this->stock_received_docket)->last();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price_purchase' =>  $lastStockReceivedDocket ? $lastStockReceivedDocket['price'] : 0,
            'price' => $this->price,
            'price_final' => $this->price_final,
            'discount_percent' => $this->discount_percent,
            'category_id' => $this->category->id,
            'category' => $this->category->name,
            'category_parent' => $this->category->parent ? $this->category->parent->name : null,
            'category_url' => $this->category->url,
            'brand' => $this->brand->name,
            'brand_id' => $this->brand->id,
            'status' => $this->status,
            'total_export' => $this->inventories->sum('total_export'),
            'total_final' => $this->inventories->sum('total_export'),
            'total_likes' => $this->favorites->count(),
            'image' => $firstColorImage,
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
            })->toArray(),
            'inventories' => $this->inventories
                ->groupBy('month_year')
                ->map(function ($groupedItems, $monthYear) {
                $totalExport = $groupedItems->sum('total_export');
                return [
                    'month_year' => $monthYear,
                    'totalExport' => $totalExport,
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
            })->toArray(),
            'reviews' => [
                'average_star_rating' => (round($this->reviews->avg('star') * 4) / 4) > 0 ? (round($this->reviews->avg('star') * 4) / 4) : 0,
                'fitted_value_distribution' => $this->reviews->count() > 0 ? [
                    'value_1' => round($this->reviews->where('fitted_value', 1)->count() / $this->reviews->count() * 100, 2),
                    'value_2' => round($this->reviews->where('fitted_value', 2)->count() / $this->reviews->count() * 100, 2),
                    'value_3' => round($this->reviews->where('fitted_value', 3)->count() / $this->reviews->count() * 100, 2),
                ] : [
                    'value_1' => 0,
                    'value_2' => 0,
                    'value_3' => 0,
                ],
                'items' => $this->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'user' => $review->user->name,
                        'classify' => $review->classify,
                        'date' => $review->date,
                        'star' => $review->star,
                        'fitted_value' => $review->fitted_value,
                        'content' => $review->content,
                        'reply' => $review->reply,
                        'status' => $review->status,
                        'images' => $review->review_image->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'image' => $image->image
                            ];
                        }),
                    ];
                })
            ], 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            
        ];
    }

}
