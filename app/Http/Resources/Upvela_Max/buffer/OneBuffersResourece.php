<?php

namespace App\Http\Resources\Upvela_Max\buffer;

use Illuminate\Http\Resources\Json\JsonResource;

class OneBuffersResourece extends JsonResource
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
            'img'=>$this->img,
            'plan_name'=>$this->plan_name,
            'price_plan'=>$this->price_plan,
            'amount'=>$this->amount,
            'activate'=>$this->activate,
            'message'=>$this->message,
            'count_sub'=>$this->count_sub,
                'percentages' => [
                $this->precantage,
                $this->precantage2,
                $this->precantage3,
                $this->precantage4 ?? '10'
            ],
            'alldesc'=>$this->alldesc,
        ];
    }
}
