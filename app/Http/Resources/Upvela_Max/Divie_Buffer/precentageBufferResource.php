<?php

namespace App\Http\Resources\Upvela_Max\Divie_Buffer;

use Illuminate\Http\Resources\Json\JsonResource;

class precentageBufferResource extends JsonResource
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
            'totle_money'=>$this->totle_all_mony,
            'buffer_name'=>$this->buffer_name,
            'precentage'=>$this->precentage,
            'money_after_divite'=>$this->totle_mony_afert_divite,
            'is_active'=>$this->is_active,
            'created_at'=>$this->created_at,
        ];
    }
}
