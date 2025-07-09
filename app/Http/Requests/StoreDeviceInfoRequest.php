<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDeviceInfoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'overlay_permission' => 'required|boolean',
            'app_version' => 'nullable|string|max:50',
            'android_version' => 'nullable|string|max:50',
            'device_id' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'overlay_permission.required' => 'El permiso de superposición es obligatorio.',
            'device_id.required' => 'El device_id es obligatorio.',
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

