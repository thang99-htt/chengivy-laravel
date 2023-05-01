<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImportCouponResource extends JsonResource
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
            'staff' => $this->staff->name,
            'supplier' => $this->supplier,
            'payment_voucher' => $this->payment_voucher,
            'date' => $this->date,
            'total_price' => $this->total_price,
            'value_added' => $this->value_added,
            'total_value' => $this->total_value,
            'image' => $this->image,
            'products' => $this->import_coupon_product->map(function ($product) {
                return [
                    'id' => $product->product->id,
                    'category' => $product->product->category->name,
                    'name' => $product->product->name,
                    'description' => $product->product->description,
                    'image' => $product->product->image,
                    'price' => $product->product->price,
                    'type' => $product->product->type->name,
                    'discount_percent' => $product->product->discount_percent,
                    'quantity' => $product->quantity,
                    'purchase_price' => $product->price,
                ];
            }), 

        ];
    }
}
