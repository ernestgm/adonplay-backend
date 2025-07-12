<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDeviceInfoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'overlay_permission' => 'sometimes|required|boolean',
            'app_version' => 'nullable|string|max:50',
            'android_version' => 'nullable|string|max:50',
            'device_id' => 'sometimes|required|string|max:255',
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
            'status_code' => 422,
            'errors'      => $validator->errors()
        ], 422));
    }
}

