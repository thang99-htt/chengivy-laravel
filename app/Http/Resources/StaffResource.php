<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
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
        $staff_address = $this->address;
        // Tìm vị trí của dấu phẩy
        $comma_position = strpos($staff_address, ',');
        // Tách chuỗi thành hai phần dựa trên vị trí của dấu phẩy
        $address_detail = trim(substr($staff_address, 0, $comma_position));
        $address = trim(substr($staff_address, $comma_position + 1));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'identity_card' => $this->identity_card,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'address' => $address,
            'address_detail' => $address_detail,
            'avatar' => $this->avatar,
            'actived' => $this->actived,
            'roles' => $this->role_staff->map(function ($role) {
                return [
                    'id' => $role->role->id,
                    'name' => $role->role->name,
                    'description' => $role->role->description,
                    'permissions' => $role->role->permission_role->map(function ($permission) {
                        return [
                            'id' => $permission->permission->id,
                            'name' => $permission->permission->name,
                            'description' => $permission->permission->description,
                        ];
                    }),
                ];
            }),
            
        ];
    }
}
