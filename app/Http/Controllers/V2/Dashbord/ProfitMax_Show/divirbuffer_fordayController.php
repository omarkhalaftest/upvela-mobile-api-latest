<?php

namespace App\Http\Controllers\V2\Dashbord\ProfitMax_Show;

use App\Http\Controllers\Controller;
use App\Models\buffer;
use Illuminate\Http\Request;
use App\Models\buffers_days;


class divirbuffer_fordayController extends Controller
{
    public function onedayInBuffer(Request $request)
    {
        if($request['active']=="active")
        {


            $buffer_days=buffers_days::where('active',1)->get();

            $buffer_days->each(function($items){

               $buffer_id=buffer::find($items->buffer_id);
               $items->buffer_id=$buffer_id->name;
            });

            return $buffer_days;

        }else{

            $buffer_days=buffers_days::get();

            $buffer_days->each(function($items){

               $buffer_id=buffer::find($items->buffer_id);
               $items->buffer_id=$buffer_id->name;
            });

            return $buffer_days;
        }




    }
}
