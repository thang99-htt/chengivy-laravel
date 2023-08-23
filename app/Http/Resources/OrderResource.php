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
        // Chuỗi địa chỉ ban đầu
        $address = $this->address_receiver;
        // Tìm vị trí của dấu phẩy
        $comma_position = strpos($address, ',');
        // Tách chuỗi thành hai phần dựa trên vị trí của dấu phẩy
        $user_address_detail = trim(substr($address, 0, $comma_position));
        $user_address = trim(substr($address, $comma_position + 1));

        return [
            'id' => $this->id,
            'staff' => $this->staff,
            'payment_method' => $this->payment_method,
            'voucher' => $this->voucher,
            'total_value' => $this->total_value,
            'fee' => $this->fee,
            'total_discount' => $this->total_discount,
            'total_price' => $this->total_price,
            'paid' => $this->paid,
            'status' => $this->status,
            'ordered_at' => $this->ordered_at,
            'confirmed_at' => $this->confirmed_at,
            'estimated_at' => $this->estimated_at,
            'cancled_at' => $this->cancled_at,
            'receipted_at' => $this->receipted_at,
            'note' => $this->note,
        
            'user_account_detail' => $this->user,
            'name_receiver' => $this->name_receiver,
            'phone_receiver' => $this->phone_receiver,
            'user_address_detail' => $user_address_detail,
            'user_address' => $user_address,

            'items' => $this->order_product->map(function ($orderDetail) {
                return [
                    'id' => $orderDetail->product->id,
                    'name' => $orderDetail->product->name,
                    'image' => $orderDetail->product->product_image[0]->image,
                    'size' => $orderDetail->size,
                    'color' => $orderDetail->color,
                    'price' => $orderDetail->product->price,
                    'price_discount' => $orderDetail->price_discount,
                    'quantity' => $orderDetail->quantity,
                ];
            }),          
        ];
    }
}
