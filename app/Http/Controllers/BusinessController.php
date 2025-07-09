<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreBusinessRequest;
use App\Http\Requests\UpdateBusinessRequest;

class BusinessController extends Controller
{
    // Listar negocios del usuario autenticado
    public function index()
    {
        $user = Auth::user();
        // Solo negocios del usuario autenticado
        return response()->json($user->businesses);
    }

    // Listar todos los negocios (solo para administradores)
    public function all()
    {
        $user = Auth::user();
        if (!$user->roles()->where('code', 'admin')->exists()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json(Business::all());
    }

    // Crear un nuevo negocio, permitiendo asignar owner si es admin
    public function store(StoreBusinessRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();
        if ($user->roles()->where('code', 'admin')->exists() && isset($validated['owner_id'])) {
            $ownerId = $validated['owner_id'];
        } else {
            $ownerId = $user->id;
        }
        $business = Business::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id' => $ownerId,
        ]);
        return response()->json($business, 201);
    }

    // Ver un negocio específico
    public function show($id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($id);
        // Solo el owner o admin puede ver el negocio
        if (!$user->roles()->where('code', 'admin')->exists() && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json($business);
    }

    // Actualizar un negocio
    public function update(UpdateBusinessRequest $request, $id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($id);
        // Solo el owner o admin puede actualizar el negocio
        if (!$user->roles()->where('code', 'admin')->exists() && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $validated = $request->validated();
        if ($user->roles()->where('code', 'admin')->exists() && isset($validated['owner_id'])) {
            $business->owner_id = $validated['owner_id'];
        }
        $business->update($validated);
        return response()->json($business);
    }

    // Eliminar un negocio
    public function destroy($id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($id);
        // Solo el owner o admin puede eliminar el negocio
        if (!$user->roles()->where('code', 'admin')->exists() && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $business->delete();
        return response()->json(['message' => 'Negocio eliminado']);
    }
}
