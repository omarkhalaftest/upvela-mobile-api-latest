<?php

namespace App\Http\Resources\Upvela_Max\buffer;

use Illuminate\Http\Resources\Json\JsonResource;

class allSubBuffersResourece extends JsonResource
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
            'buffer_name'=>$this->buffer_name,
            'buufer_img'=>$this->buufer_img,
            'start_subscrip'=>$this->start_subscrip,
            'end_subscrip'=>$this->end_subscrip,
            'amount'=>$this->amount,
            'active'=>$this->active,
            'HashID'=>$this->HashID,
        ];
    }
}
