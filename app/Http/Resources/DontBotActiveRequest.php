<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DontBotActiveRequest extends JsonResource
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
            
        'status'=>$this->status ?? 0,
        'last_tp'=>$this->last_tp,

        ];
    }
}
