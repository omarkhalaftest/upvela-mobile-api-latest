<?php

namespace App\Http\Controllers\VersionApp;

use App\Http\Controllers\Controller;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;

class versionAppController extends Controller
{
    use ResponseJson;
    public function version()
    {
        $version=2.1;
         return response()->json(
            [
                'success' => true,
                'data' => $version,
                // 'status' => $status,



            ]
        );
        return $this->successDtat($version);
    }
}
