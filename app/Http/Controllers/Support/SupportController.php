<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function support()
    {
        $email="support@upvela.com";
        $phone='201033910402';
        return response()->json([
            'email'=>$email,
            'phone'=>$phone,
        ]);
    }
}
