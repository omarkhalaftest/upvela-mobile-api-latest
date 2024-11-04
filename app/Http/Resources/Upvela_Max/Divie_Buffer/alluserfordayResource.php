<?php

namespace App\Http\Resources\Upvela_Max\Divie_Buffer;

use Illuminate\Http\Resources\Json\JsonResource;

class alluserfordayResource extends JsonResource
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
            'id'=>$this->id,
            'username'=>$this->username,
            'buffer_name'=>$this->buffer_name,
            'daysRemaining'=>$this->daysRemaining,
            'money_for_day'=>$this->money_for_day,
            'mony_for_15day'=>$this->mony_for_15day,
            'active'=>$this->active,
            'created_at'=>$this->created_at,
        ];
    }
}
