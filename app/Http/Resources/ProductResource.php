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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'final_price' => $this->when(isset($this->final_price), $this->final_price),
            'discount_percent' => $this->discount_percent,
            'image' => $this->image,
            'category' => $this->category->name,
            'category_parent' => $this->category->parent ? $this->category->parent->name : null,
            'category_url' => $this->category->url,
            'type' => $this->type->description,
            'color' => $this->color->description,
            'status' => $this->status,
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => $image->image
                ];
            }), 
            'sizes' => $this->product_size->map(function ($size) {
                return [
                    'id' => $size->id,
                    'size_id' => $size->size_id,
                    'size_name' => $size->size->name,
                    'quantity' => $size->quantity,
                    'stock' => $size->stock,
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

}
