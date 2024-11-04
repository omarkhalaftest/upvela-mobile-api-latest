<?php

namespace App\Http\Requests\Upvela_Max\buffer;

use App\Models\buffer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class ActivateBufferRequest extends FormRequest
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
            'buffer_id' => 'required||exists:buffers,id||integer',
            'per_month' => 'integer||in:1,3,6,12'
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
