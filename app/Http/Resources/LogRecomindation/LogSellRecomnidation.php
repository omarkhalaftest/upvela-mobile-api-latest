<?php

namespace App\Http\Resources\LogRecomindation;

use Illuminate\Http\Resources\Json\JsonResource;

class LogSellRecomnidation extends JsonResource
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
            'quantity'=>$this->quantity,
            'price'=>$this->price,
            'status'=>$this->status,
            'massageError'=>$this->massageError,
            'bot_num'=>$this->bot_num,
            'symbol'=>$this->symbol,
            'side'=>$this->side,
            'profit_usdt'=>$this->profit_usdt,
            'fees'=>$this->fees,
            'created_at'=>$this->created_at,
            'side'=>$this->side,
            'name'=>$this->name,
            'email'=>$this->email,


        ];
    }
}
