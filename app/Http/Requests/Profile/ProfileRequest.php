<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Tymon\JWTAuth\Contracts\Providers\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // You can add any authorization logic here if needed
        return true;
    }
    // protected function prepareForValidation()
    // {
    //     if ($this->user_id) {
    //         $this->merge([
    //             'user_id' => $this->user()->id
    //         ]);
    //     }
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $this->user()->id,
            'password' => 'nullable|string|min:8',
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
