<?php

namespace App\Http\Controllers\V2\Dashbord\ProfitMax_Show;

use App\Models\buffer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\profit_buffer_to_user;
use App\Http\Resources\Upvela_Max\Divie_Buffer\alluserfordayResource;



class divirbuffer_for_userController extends Controller
{
    public function divite_for_user(Request $request)
    {
                set_time_limit(120);

         
        if($request['active']=="active")
        {
              $profit_buffer_to_user=profit_buffer_to_user::where('active',1)->get();
            $profit_buffer_to_user->each(function($items){

                $buffer_id=buffer::find($items->buffer_id);
                $items->buffer_name=$buffer_id->name;

                $user=User::find($items->user_id);
                $items->username=$user->name;
             });

             return alluserfordayResource::collection($profit_buffer_to_user);

        }else{
                    $page = $request->input('page', 1); // Get the requested page from the request parameters

              $profit_buffer_to_user=profit_buffer_to_user::paginate(50, ['*'], 'page', $page);
            $profit_buffer_to_user->each(function($items){

                $buffer_id=buffer::find($items->buffer_id);
                $items->buffer_name=$buffer_id->name;

                $user=User::find($items->user_id);
                $items->username=$user->name;
             });

              
             
             return response()->json([
                'data' => alluserfordayResource::collection($profit_buffer_to_user),
                'meta' => [
                    'current_page' => $profit_buffer_to_user->currentPage(),
                    'last_page' => $profit_buffer_to_user->lastPage(),
                    'total' => $profit_buffer_to_user->total(),
                    'next_page' => $profit_buffer_to_user->nextPageUrl(),
                ],
            ]);
        }
    }


        }

    

