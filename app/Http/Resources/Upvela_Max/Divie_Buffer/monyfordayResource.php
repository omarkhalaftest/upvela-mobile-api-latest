<?php

namespace App\Http\Resources\Upvela_Max\Divie_Buffer;

use Illuminate\Http\Resources\Json\JsonResource;

class monyfordayResource extends JsonResource
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
            'precentage__from_totle'=>$this->precentage,
            'money_after_divite'=>$this->totle_mony_afert_divite,
            'is_active'=>$this->is_active,
            'count_user'=>$this->count_user,
            "after_divite_on_count_user"=>$this->after_divite_on_countuser,
            "precantage_buufer"=>$this->precantage,
            "after_diviate_precantage_for_15day"=>$this->after_diviate_precantage,
            "forday"=>$this->forday,
            'created_at'=>$this->created_at,
        ];
    }
}
