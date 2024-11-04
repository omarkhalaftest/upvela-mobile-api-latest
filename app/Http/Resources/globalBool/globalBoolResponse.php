<?php

namespace App\Http\Resources\globalBool;

use Illuminate\Http\Resources\Json\JsonResource;

class globalBoolResponse extends JsonResource
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
            'name' => $this->name,
            'money' => 150,
            'percentage' => 5,
            'plna' => 'VIP1',

        ];
    }
}
