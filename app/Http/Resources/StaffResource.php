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
            'roles' => $this->role_staff->map(function ($role) {
                return [
                    'role_staff' => $role->id,
                    'id' => $role->role->id,
                    'name' => $role->role->name,
                    'description' => $role->role->description,
                    'permissions' => $role->role->permission_role->map(function ($permission) {
                        return [
                            'permission_role' => $permission->id,
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
