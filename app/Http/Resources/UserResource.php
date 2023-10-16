<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->profiles->first()->name,
            'email' => $this->email,
            'password' => $this->password,
            'gender' => $this->profiles->first()->gender,
            'birth_date' => $this->profiles->first()->birth_date,
            'phone' => $this->profiles->first()->phone,
            'bank_account' => $this->profiles->first()->bank_account,
            'delivery_address' => $this->delivery_address->map(function ($address) {
                // Chuỗi địa chỉ ban đầu
                $address_user = $address->address;
                // Tìm vị trí của dấu phẩy
                $comma_position = strpos($address_user, ',');
                // Tách chuỗi thành hai phần dựa trên vị trí của dấu phẩy
                $user_address_detail = trim(substr($address_user, 0, $comma_position));
                $user_address = trim(substr($address_user, $comma_position + 1));

                return [
                    'id' => $address->id,
                    'name' => $address->name,
                    'phone' => $address->phone,
                    'address_detail' => $user_address_detail,
                    'address' => $user_address,
                    'default' => $address->default,
                ];
            })
        ];
    }

}
