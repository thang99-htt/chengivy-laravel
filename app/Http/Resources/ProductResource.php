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
            'discount_percent' => $this->discount_percent,
            'image' => $this->image,
            'category' => $this->category->name,
            'type' => $this->type->name,
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
            'created' => $this->created,
            'updated' => $this->updated,
            'delete' => $this->delete,
            
        ];
    }

}
