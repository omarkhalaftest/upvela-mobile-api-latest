<?php

namespace App\Http\Controllers\globalBool;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\globalBool\globalBoolResponse;

class globalController extends Controller
{
    public function globalBoll()
    {
        $users = User::take(10)->get();

        return globalBoolResponse::collection($users);
    }
}
