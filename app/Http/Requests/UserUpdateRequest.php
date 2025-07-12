<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $this->route('id'),
            'password' => 'sometimes|string|min:6|confirmed',
            'phone' => 'nullable|numeric|digits:10',
            'enabled' => 'boolean',
            'role' => 'sometimes|exists:roles,id',
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

    public function messages(): array
    {
        return [
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder 255 caracteres.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'phone.numeric' => 'El teléfono debe ser numérico.',
            'phone.digits' => 'El teléfono debe tener exactamente 10 dígitos.',
            'enabled.boolean' => 'El campo habilitado debe ser verdadero o falso.',
            'role.exists' => 'El rol seleccionado no existe.',
        ];
    }
}
