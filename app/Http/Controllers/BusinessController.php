<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreBusinessRequest;
use App\Http\Requests\UpdateBusinessRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    // Listar negocios del usuario autenticado
    public function index()
    {
        $user = Auth::user();
        // Solo negocios del usuario autenticado
        if (!$this->isAdmin($user)) {
            $businesses = $user->businesses()->with('owner')->get();
            return response()->json($businesses);
        }
        // Si es admin, listar todos los negocios

        return response()->json(Business::with('owner')->get());
    }

    // Crear un nuevo negocio, permitiendo asignar owner si es admin
    public function store(StoreBusinessRequest $request)
    {
        $user = Auth::user();
        // Solo admin puede crear negocios con owner_id diferente al suyo
        if (!$this->isAdmin($user)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validated();
        $business = Business::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id' => $validated['owner_id'],
        ]);
        return response()->json($business, 201);
    }

    // Ver un negocio específico
    public function show($id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($id);
        // Solo el owner o admin puede ver el negocio
        if (!$this->isAdmin($user) && $business->owner_id !== $user->id) {
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
        if (!$this->isAdmin($user) && $business->owner_id !== $user->id) {
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
    public function destroy(Request $request)
    {
        $user = Auth::user();
        // Solo admin puede eliminar negocios
        if (!$this->isAdmin($user)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $ids = $request->input('ids');
        $validator = Validator::make(['ids' => $ids], [
            'ids' => 'required|array',
            'ids.*' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], app('VALIDATION_STATUS'));
        }
        $deleted = DB::table('businesses')->whereIn('id', $ids)->delete();
        return response()->json([
                'success' => true,
                'message' => "$deleted record(s) deleted."
            ]
        );
    }
}
