<?php

namespace App\Http\Resources\Upvela_Max\buffer;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanBuffersResourece extends JsonResource
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
            'name'=>$this->name,
            'price'=>$this->price,
            'number_bot'=>$this->number_bot,
            'buffer'=>$this->buffer,
            'discount'=>$this->discount,
        ];
    }
}
