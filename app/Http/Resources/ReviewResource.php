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
            'user' => $this->user->name,
            'date' => $this->date,
            'star' => $this->star,
            'fitted_value' => $this->fitted_value,
            'content' => $this->content,
            'status' => $this->status,
            'product' => $this->product,
            'classify' => $this->classify,
            'images' => $this->review_image,
            'reply' => $this->reply
        ];
    }
}
