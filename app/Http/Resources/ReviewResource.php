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
        return [
            'id' => $this->id,
            'content' => $this->content,
            'rate' => $this->rate,
            'status' => $this->status,
            'created_at' => $this->created_at,
        
            'user' => $this->user->name,

            'product' => $this->product,
            
            'images' => $this->images_review
        ];
    }
}
