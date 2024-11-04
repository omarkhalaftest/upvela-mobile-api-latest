<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $endPlan = $this->end_plan;
        if ($this->plan->nameChannel == 'FREE') {
            $endPlan = 0;
        }
        if ($this->affiliate_code) {
            $userFathers = User::where('comming_afflite', $this->affiliate_code)->get();
            $userFathersCount = count($userFathers);
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'state' => $this->state,
            'phone' => $this->phone,
            'is_bot'=>$this->is_bot,
            'money' => $this->money,
            'plan' => $this->plan,
            'end_plan' =>  $endPlan,
            'start_plan' => $this->start_plan,
            'number_of_user' => $userFathersCount,
            'affiliate_code' => $this->affiliate_code,
            'comming_afflite' => $this->comming_afflite,
            'binanceApiKey' => $this->binanceApiKey,
            'binanceSecretKey' => $this->binanceSecretKey,
            'number_points' => $this->number_points,
            'Role' => UserNameResource::collection($this->whenLoaded('Role')),
            'bot_transfer' => BotResources::collection($this->whenLoaded('bot_transfer')),
            'binanceloges' => $this->binanceloges,

        ];
    }
}
