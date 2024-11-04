<?php

namespace App\Http\Resources\LogRecomindation;

use App\Http\Resources\User\NameEmailResourec;
use Illuminate\Http\Resources\Json\JsonResource;

class LogBuyRecomnidation extends JsonResource
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
            'user_id'=>$this->id,
            'side'=>$this->side,
            'quantity'=>$this->quantity,
            'symbol'=>$this->symbol,
            'price'=>$this->price,
            'status'=>$this->status,
            'massageError'=>$this->massageError,
            'bot_num'=>$this->bot_num,
            'name'=>$this->name,
            'email'=>$this->email,
            'created_at'=>$this->created_at,





        ];
    }
}
