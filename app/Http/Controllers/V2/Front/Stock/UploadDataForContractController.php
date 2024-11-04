<?php

namespace App\Http\Controllers\V2\Front\Stock;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\UploadDataContractRequest;
use App\Http\Controllers\Helper\InterventionImage\InterventionController;
use App\Models\contract_users_data;
use App\Traits\ResponseJson;
use Dompdf\Dompdf;


class UploadDataForContractController extends Controller
{
    use ResponseJson;

    public function upload(UploadDataContractRequest $request)
    {
        $user = auth('api')->user();
        $images = ['front_id_image', 'back_id_image', 'face_image'];
        $folders = ['front_ids', 'back_ids', 'faces'];

        $uploadedImages = [];

        $InterventionController = new InterventionController();


        foreach ($images as $index => $img) {
            if ($request->hasFile($img)) {
                $uploadedImages[$img] = $InterventionController->uploadImage($request->file($img), $folders[$index]);
            } elseif ($img !== 'back_id_image') { // Check if the image is not 'back_id_image'
                return $this->error("Upload Img");
            }
        }
        $contract_users_data = contract_users_data::create([
            'user_id' => $user->id,
            'fullname' => $request->input('fullname'),
            'international_id' => $request->input('international_id'),
            'front_id_image' => $uploadedImages['front_id_image'] ?? null,
            'back_id_image' => $uploadedImages['back_id_image'] ?? null,
            'face_image' => $uploadedImages['face_image'] ?? null,
            'active' => 0,

        ]);

        return $this->success("Data User has been successfully stored");
    }
}
