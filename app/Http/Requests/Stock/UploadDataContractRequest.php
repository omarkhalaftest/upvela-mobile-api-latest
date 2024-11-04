<?php

namespace App\Http\Requests\Stock;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class UploadDataContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'fullname' => 'required|string',
            'international_id' => 'required|unique:contract_users_datas,international_id',
            'front_id_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'back_id_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'face_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function failedValidation(Validator $validator)

    {

        throw new HttpResponseException(response()->json([

            'success'   => false,

            'message'   => 'Validation errors',

            'data'      => $validator->errors()

        ]));
    }
}
