<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'payment' => $this->payment,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'order_date' => $this->order_date,
            'estimate_date' => $this->estimate_date,
            'cancle_date' => $this->cancle_date,
            'receipt_date' => $this->receipt_date,
        
            'user_detail' => $this->user,
            'user_name' => $this->contact->name,
            'user_phone' => $this->contact->phone,
            'user_address' => $this->contact->address,

            'ward' => $this->contact->ward->name,
            'district' => $this->contact->ward->district->name,
            'city' => $this->contact->ward->district->city->name,

            'order_details' => $this->order_product->map(function ($orderDetail) {
                return [
                    'product_name' => $orderDetail->product->name,
                    'product_image' => $orderDetail->product->image,
                    'product_size' => $orderDetail->size,
                    'product_price' => $orderDetail->product->price,
                    'product_quantity' => $orderDetail->quantity,
                    'product_into_money' => $orderDetail->price,
                ];
            }),          
        ];
    }
}
