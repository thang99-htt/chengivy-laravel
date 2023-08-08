<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'permissions' => $this->permission_role->map(function ($permissions) {
                return [
                    'id' => $permissions->permission->id,
                    'name' => $permissions->permission->name,
                    'description' => $permissions->permission->description,
                ];
            }), 
        ];
    }
}
