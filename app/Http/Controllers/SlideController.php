<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSlideRequest;
use App\Http\Requests\UpdateSlideRequest;

class SlideController extends Controller
{
    // Listar slides de un business
    public function index($businessId)
    {
        $user = Auth::user();
        $business = Business::findOrFail($businessId);
        // Solo el owner del business o admin puede ver los slides
        if (!$user->roles()->where('code', 'admin')->exists() && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json($business->slides);
    }

    // Crear slide en un business
    public function store(StoreSlideRequest $request, $businessId)
    {
        $user = Auth::user();
        $business = Business::findOrFail($businessId);
        // Solo el owner del business o admin puede crear slides
        if (!$user->roles()->where('code', 'admin')->exists() && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $slide = $business->slides()->create($request->validated());
        return response()->json($slide, 201);
    }

    // Ver un slide específico
    public function show($businessId, $id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($businessId);
        // Solo el owner del business o admin puede ver el slide
        if (!$user->roles()->where('code', 'admin')->exists() && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $slide = $business->slides()->findOrFail($id);
        return response()->json($slide);
    }

    // Actualizar un slide
    public function update(UpdateSlideRequest $request, $businessId, $id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($businessId);
        // Solo el owner del business o admin puede actualizar slides
        if (!$user->roles()->where('code', 'admin')->exists() && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $slide = $business->slides()->findOrFail($id);
        $slide->update($request->validated());
        return response()->json($slide);
    }

    // Eliminar un slide
    public function destroy($businessId, $id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($businessId);
        // Solo el owner del business o admin puede eliminar slides
        if (!$user->roles()->where('code', 'admin')->exists() && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $slide = $business->slides()->findOrFail($id);
        $slide->delete();
        return response()->json(['message' => 'Slide eliminado']);
    }
}
