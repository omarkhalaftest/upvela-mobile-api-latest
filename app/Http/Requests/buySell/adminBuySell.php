<?php

namespace App\Http\Requests\buySell;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class adminBuySell extends FormRequest
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

            'ticker'=>'required|string',
            'type'=>'required|string',
            'target1'=>'required',
            'target2'=>'required',
            'stoplose'=>'required',

        ];
    }

    public function failedValidation(Validator $validator)

    {

        throw new HttpResponseException(response()->json([

            'success'   => false,

            'message'   => 'Validation errors',

            'error'      => $validator->errors()

        ]));

    }
}
