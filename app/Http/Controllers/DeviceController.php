<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceInfo;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;

class DeviceController extends Controller
{
    // Listar device del usuario autenticado o todos si es admin
    public function index()
    {
        $user = Auth::user();
        if ($user->roles()->where('code', 'admin')->exists()) {
            return response()->json(Device::all());
        }
        return response()->json(Device::where('user_id', $user->id)->get());
    }

    // Crear un nuevo device
    public function store(StoreDeviceRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $device = Device::create($data);
        return response()->json($device, 201);
    }

    // Ver un device específico
    public function show($id)
    {
        $user = Auth::user();
        $device = Device::findOrFail($id);
        if (!$user->roles()->where('code', 'admin')->exists() && $device->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json($device);
    }

    // Actualizar un device
    public function update(UpdateDeviceRequest $request, $id)
    {
        $user = Auth::user();
        $device = Device::findOrFail($id);
        if (!$user->roles()->where('code', 'admin')->exists() && $device->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $device->update($request->validated());
        return response()->json($device);
    }

    // Eliminar un device
    public function destroy($id)
    {
        $user = Auth::user();
        $device = Device::findOrFail($id);
        if (!$user->roles()->where('code', 'admin')->exists() && $device->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        // Eliminar DeviceInfo asociado por device_id
        DeviceInfo::where('device_id', $device->device_id)->delete();
        $device->delete();
        return response()->json(['message' => 'Device eliminado']);
    }
}
