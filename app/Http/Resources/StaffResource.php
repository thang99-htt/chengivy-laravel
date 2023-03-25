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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role_staff' => $this->role_staff->map(function ($role_staff) {
                return [
                    'id' => $role_staff->id,
                ];
            }), 
            'roles' => $this->role_staff->map(function ($roles) {
                return [
                    'id' => $roles->role->id,
                    'name' => $roles->role->name,
                    'description' => $roles->role->description,
                ];
            }), 
        ];
    }
}
