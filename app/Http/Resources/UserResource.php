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
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'profiles' => [
                'name' => $this->name,
                'gender' => $this->profiles->first()->gender,
                'birth_date' => $this->profiles->first()->birth_date,
                'phone' => $this->profiles->first()->phone,
                'account_number' => $this->profiles->first()->account_number,
            ],
            'contacts' => $this->contacts->map(function ($contact) {
                return [
                    'name' => $contact->name,
                    'phone' => $contact->phone,
                    'address' => $contact->address,
                    'ward' => $contact->ward->name,
                    'district' => $contact->ward->district->name,
                    'city' => $contact->ward->district->city->name,
                ];
            })
        ];
    }

}
