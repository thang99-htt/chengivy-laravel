<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockReceivedDocketResource extends JsonResource
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
            'staff' => $this->staff,
            'staff_id' => $this->staff->id,
            'supplier' => $this->supplier,
            'supplier_id' => $this->supplier->id,
            'payment_voucher' => $this->payment_voucher,
            'payment_voucher_id' => $this->payment_voucher_id,
            'date' => $this->date,
            'form' => $this->form,
            'total_price' => $this->total_price,
            'value_added' => $this->value_added,
            'total_value' => $this->total_value,
            'image' => $this->image,
            'description' => $this->description,
            'items' => $this->stock_received_docket_product->map(function ($item) {
                $colorGroups = $item->product->product_image // Use $item instead of $this->stock_received_docket_product->product
                    ->groupBy('color_id')
                    ->sortBy('color_id');
            
                $firstColorGroup = $colorGroups->first();
                $firstColorImage = null;
            
                if ($firstColorGroup) {
                    $firstColorImage = $firstColorGroup
                        ->first()
                        ->image;
                }

                return [
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'product_image' => $firstColorImage,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'price_purchase' => $item->price,
                ];
            }),
            'inventories' => $this->stock_received_docket_product->flatMap(function ($item) {
                return $item->stock_received_docket_product_detail->map(function ($detailItem) {
                    return [
                        'product_id' => $detailItem->product->id,
                        'color_id' => $detailItem->color->id,
                        'color_name' => $detailItem->color->name,
                        'color' => $detailItem->color->color,
                        'size_id' => $detailItem->size->id,
                        'size_name' => $detailItem->size->name,
                        'quantity' => $detailItem->quantity,
                    ];
                });
            }),
            
        ];
    }
}
