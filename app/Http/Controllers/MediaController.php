<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Slide;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    // Listar media de un slide
    public function index($slideId)
    {
        $user = Auth::user();
        $slide = Slide::findOrFail($slideId);
        $business = $slide->business;
        if (!$this->isAdmin($user) && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json($slide->medias);
    }

    // Subir media a un slide
    public function store(StoreMediaRequest $request, $slideId)
    {
        $user = Auth::user();
        $slide = Slide::findOrFail($slideId);
        $business = $slide->business;
        if (!$this->isAdmin($user) && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $type = $request->input('type');
        $file = $request->file('file');
        $audio = $request->file('audio');
        $userId = $business->owner_id;
        $folder = $type === 'image' ? 'images' : 'videos';
        $fileName = $folder . '/' . $userId . '/' . $folder . '/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('ftp')->put($fileName, fopen($file, 'r+'));
        $audioPath = null;
        if ($type === 'image' && $audio) {
            $audioName = 'audios/' . $userId . '/audios/' . Str::uuid() . '.' . $audio->getClientOriginalExtension();
            Storage::disk('ftp')->put($audioName, fopen($audio, 'r+'));
            $audioPath = $audioName;
        }
        $media = $slide->medias()->create([
            'type' => $type,
            'file_path' => $fileName,
            'audio_path' => $audioPath,
        ]);
        return response()->json($media, 201);
    }

    // Ver media específica
    public function show($slideId, $id)
    {
        $user = Auth::user();
        $slide = Slide::findOrFail($slideId);
        $business = $slide->business;
        if (!$this->isAdmin($user) && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $media = $slide->medias()->findOrFail($id);
        return response()->json($media);
    }

    // Actualizar media
    public function update(UpdateMediaRequest $request, $slideId, $id)
    {
        $user = Auth::user();
        $slide = Slide::findOrFail($slideId);
        $business = $slide->business;
        if (!$this->isAdmin($user) && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $media = $slide->medias()->findOrFail($id);
        $data = $request->validated();
        if ($request->hasFile('file')) {
            $type = $request->input('type', $media->type);
            $file = $request->file('file');
            $userId = $business->owner_id;
            $folder = $type === 'image' ? 'images' : 'videos';
            $fileName = $folder . '/' . $userId . '/' . $folder . '/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('ftp')->put($fileName, fopen($file, 'r+'));
            $data['file_path'] = $fileName;
        }
        if ($request->hasFile('audio') && ($request->input('type', $media->type) === 'image')) {
            $audio = $request->file('audio');
            $userId = $business->owner_id;
            $audioName = 'audios/' . $userId . '/audios/' . Str::uuid() . '.' . $audio->getClientOriginalExtension();
            Storage::disk('ftp')->put($audioName, fopen($audio, 'r+'));
            $data['audio_path'] = $audioName;
        }
        $media->update($data);
        return response()->json($media);
    }

    // Eliminar media
    public function destroy($slideId, $id)
    {
        $user = Auth::user();
        $slide = Slide::findOrFail($slideId);
        $business = $slide->business;
        if (!$this->isAdmin($user) && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $media = $slide->medias()->findOrFail($id);
        // Eliminar archivos del FTP
        if ($media->file_path) {
            Storage::disk('ftp')->delete($media->file_path);
        }
        if ($media->audio_path) {
            Storage::disk('ftp')->delete($media->audio_path);
        }
        $media->delete();
        return response()->json(['message' => 'Archivo multimedia eliminado']);
    }
}
