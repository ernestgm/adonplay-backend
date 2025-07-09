<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDeviceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:devices,code',
            'device_id' => 'required|string|max:100|unique:devices,device_id',
            'portrait' => 'required|boolean',
            'as_presentation' => 'required|boolean',
            'slide_id' => 'nullable|exists:slides,id',
            'marquee_id' => 'nullable|exists:marquees,id',
            'qr_id' => 'nullable|exists:qrs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'code.required' => 'El código es obligatorio.',
            'device_id.required' => 'El device_id es obligatorio.',
            'portrait.required' => 'El campo portrait es obligatorio.',
            'as_presentation.required' => 'El campo as_presentation es obligatorio.',
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

