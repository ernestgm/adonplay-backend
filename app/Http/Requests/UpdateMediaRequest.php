<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMediaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'sometimes|required|in:image,video',
            'file' => 'sometimes|required|file',
            'audio' => 'nullable|file|mimetypes:audio/mpeg,audio/mp3',
            'description' => 'nullable|string',
            'description_position' => 'nullable|string',
            'description_size' => 'nullable|string',
            'qr_info' => 'nullable|string',
            'qr_position' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->type === 'image' || ($this->has('type') && $this->input('type') === 'image')) {
                if ($this->hasFile('file') && (!$this->file('file')->isValid() || !in_array($this->file('file')->extension(), ['jpg','jpeg','png','gif','bmp','webp']))) {
                    $validator->errors()->add('file', 'El archivo debe ser una imagen válida.');
                }
                if ($this->hasFile('audio') && (!$this->file('audio')->isValid() || !in_array($this->file('audio')->extension(), ['mp3']))) {
                    $validator->errors()->add('audio', 'El audio debe ser un archivo MP3 válido.');
                }
            } elseif ($this->type === 'video' || ($this->has('type') && $this->input('type') === 'video')) {
                if ($this->hasFile('file') && (!$this->file('file')->isValid() || !in_array($this->file('file')->extension(), ['mp4','avi','mov','mkv','webm']))) {
                    $validator->errors()->add('file', 'El archivo debe ser un video válido.');
                }
                if ($this->hasFile('audio')) {
                    $validator->errors()->add('audio', 'No se puede adjuntar audio a un video.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'type.in' => 'El tipo debe ser image o video.',
            'file.file' => 'El archivo debe ser válido.',
            'audio.file' => 'El audio debe ser un archivo válido.',
            'audio.mimetypes' => 'El audio debe ser un archivo MP3.',
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
