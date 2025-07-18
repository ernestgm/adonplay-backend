<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMediaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|in:image,video',
            'file' => 'required',
            'description' => 'nullable|string',
            'description_position' => 'nullable|string',
            'description_size' => 'nullable|string',
            'qr_info' => 'nullable|string',
            'qr_position' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');

            // Para videos, el archivo viene como un único archivo, no como array
            if ($type === 'video') {
                $file = $this->file('file');
                if (!$file || !$file->isValid()) {
                    $validator->errors()->add('file', 'El archivo de video no se pudo subir correctamente.');
                } elseif (!in_array($file->extension(), ['mp4', 'avi', 'mov', 'mkv', 'webm'])) {
                    $validator->errors()->add('file', 'El archivo no es un video válido.');
                }
            } else {
                // Para imágenes, sigue procesando como array
                $files = $this->file('file', []);
                foreach ($files as $file) {
                    if (!$file->isValid()) {
                        $validator->errors()->add('file', 'Uno de los archivos no se pudo subir correctamente.');
                        continue;
                    }

                    if (!in_array($file->extension(), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
                        $validator->errors()->add('file', 'Uno de los archivos no es una imagen válida.');
                    }
                }
            }

            // Validar archivos de audio (solo para imágenes)
            if ($type === 'image') {
                $audios = $this->file('audio', []);
                foreach ($audios as $audio) {
                    if (!$audio->isValid() || $audio->extension() !== 'mp3') {
                        $validator->errors()->add('audio', 'Uno de los audios no es un archivo MP3 válido.');
                    }
                }
            }

            // Para tipo video: no debe haber audio
            if ($type === 'video' && $this->hasFile('audio')) {
                $validator->errors()->add('audio', 'No se puede adjuntar audio a un video.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'type.required' => 'El tipo es obligatorio.',
            'type.in' => 'El tipo debe ser image o video.',
            'file.required' => 'El archivo es obligatorio.',
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
