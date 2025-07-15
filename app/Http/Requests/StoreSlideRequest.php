<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSlideRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'business_id' => 'required|integer|exists:businesses,id',
            'description_position' => 'nullable|string',
            'description_size' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'business_id.required' => 'El Negocio es obligatorio.',
            'description_position.string' => 'La posición de la descripción debe ser una cadena de texto.',
            'description_size.string' => 'El tamaño de la descripción debe ser una cadena de texto.',
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
