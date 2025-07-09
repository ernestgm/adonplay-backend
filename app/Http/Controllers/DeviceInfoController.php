<?php

namespace App\Http\Controllers;

use App\Models\DeviceInfo;
use App\Http\Requests\StoreDeviceInfoRequest;
use App\Http\Requests\UpdateDeviceInfoRequest;
use Illuminate\Support\Facades\Auth;

class DeviceInfoController extends Controller
{
    // Listar todos los DeviceInfo (privado)
    public function index()
    {
        return response()->json(DeviceInfo::all());
    }

    // Crear DeviceInfo (público)
    public function store(StoreDeviceInfoRequest $request)
    {
        $deviceInfo = DeviceInfo::create($request->validated());
        return response()->json($deviceInfo, 201);
    }

    // Ver un DeviceInfo específico (privado)
    public function show($id)
    {
        $deviceInfo = DeviceInfo::findOrFail($id);
        return response()->json($deviceInfo);
    }

    // Actualizar DeviceInfo (privado)
    public function update(UpdateDeviceInfoRequest $request, $id)
    {
        $deviceInfo = DeviceInfo::findOrFail($id);
        $deviceInfo->update($request->validated());
        return response()->json($deviceInfo);
    }

    // Eliminar DeviceInfo (privado)
    public function destroy($id)
    {
        $deviceInfo = DeviceInfo::findOrFail($id);
        $deviceInfo->delete();
        return response()->json(['message' => 'DeviceInfo eliminado']);
    }
}

