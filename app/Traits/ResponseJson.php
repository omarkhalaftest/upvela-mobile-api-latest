<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ResponseJson
{

    public function success($message, $status = 200)
    {
        return response()->json(
            [
                'success' => true,
                'message' => $message,
                'status' => $status,



            ]
        );
    }

    public function error($message, $status = 400)
    {
        return response()->json(
            [
                'success' => false,
                'message' => $message,
                'status' => $status,

            ]
        );
    }
    
        public function token()
    {
        return $this->error('expire token');
    }
    public function successDtat($message, $status = 200)
    {
        return response()->json(
            [
                'success' => true,
                'data' => $message,
                'status' => $status,



            ]
        );
    }
       public function errorDtat($message, $status = 400)
    {
        return response()->json(
            [
                'success' => false,
                'data' => $message,
                'status' => $status,



            ]
        );
    }
}
