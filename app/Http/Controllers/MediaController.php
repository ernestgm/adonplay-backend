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
        $userId = $business->owner_id;
        $rootFolder = env('FTP_ENV');
        $folder = $type === 'image' ? 'images' : 'videos';

        $createdMedias = [];

        // Para videos, el archivo viene como un único archivo, no como array
        if ($type === 'video') {
            $file = $request->file('file');
            $subfolder = "$rootFolder/$folder/user_$userId/$folder/";
            $fileName = "$rootFolder/$folder/user_$userId/$folder/" . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $this->getFileSystem()->makeDirectory($subfolder);
            $this->uploadToFtp($fileName, $file);
            $filePath = $fileName;

            $media = $slide->medias()->create([
                'type' => $type,
                'file_path' => $filePath,
                'audio_path' => null, // Videos no tienen audio separado
                'description_position' => $request->input('description_position'),
                'description_size' => $request->input('description_size'),
                'qr_position' => $request->input('qr_position'),
                'duration' => $request->input('duration'),
            ]);

            $createdMedias[] = $media;
        } else {
            // Para imágenes, sigue procesando como array
            $files = $request->file('file');   // Array de archivos
            $audios = $request->file('audio'); // Puede ser null o array

            foreach ($files as $index => $file) {
                $subfolder = "$rootFolder/$folder/user_$userId/$folder/";
                $fileName = "$rootFolder/$folder/user_$userId/$folder/" . Str::uuid() . '.' . $file->getClientOriginalExtension();
                $this->getFileSystem()->makeDirectory($subfolder);
                $this->uploadToFtp($fileName, $file);
                $filePath = $fileName;

                $audioPath = null;
                if (isset($audios[$index])) {
                    $audio = $audios[$index];
                    $audioSubfolder = "$rootFolder/audios/user_$userId/audios/";
                    $audioName = $audioSubfolder . Str::uuid() . '.' . $audio->getClientOriginalExtension();
                    $this->getFileSystem()->makeDirectory($audioSubfolder);
                    $this->uploadToFtp($fileName, $audio);
                    $audioPath = $audioName;
                }

                $media = $slide->medias()->create([
                    'type' => $type,
                    'file_path' => $filePath,
                    'audio_path' => $audioPath,
                    'description_position' => $request->input('description_position'),
                    'description_size' => $request->input('description_size'),
                    'qr_position' => $request->input('qr_position'),
                    'duration' => $request->input('duration'),
                ]);

                $createdMedias[] = $media;
            }
        }

        return response()->json($createdMedias, 201);
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
        $data = $request->all();
        $rootFolder = env('FTP_ENV');

        if ($request->hasFile('file')) {
            $type = $request->input('type', $media->type);
            $file = $request->file('file');
            $userId = $business->owner_id;
            $folder = $type === 'image' ? 'images' : 'videos';
            $subfolder = "$rootFolder/$folder/user_$userId/$folder/";
            $fileName = $subfolder . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $this->getFileSystem()->makeDirectory($subfolder);
            $this->uploadToFtp($fileName, $file);
            $data['file_path'] = $fileName;
        }

        if ($request->hasFile('audio') && ($request->input('type', $media->type) === 'image')) {
            $audio = $request->file('audio');
            $userId = $business->owner_id;
            $audioSubfolder = "$rootFolder/audios/user_$userId/audios/";
            $audioName = $audioSubfolder . Str::uuid() . '.' . $audio->getClientOriginalExtension();
            $this->getFileSystem()->makeDirectory($audioSubfolder);
            $this->uploadToFtp($audioName, $audio);
            $data['audio_path'] = $audioName;
        }
        $media->update($data);
        return response()->json($data);
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
