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
        if (!$this->isAdmin($user) && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $slides = $business->slides()->with('business.owner')->get();
        return response()->json($slides);
    }

    public function all()
    {
        $user = Auth::user();
        // Solo Slides del usuario autenticado
        if (!$this->isAdmin($user)) {
            $slides = Slide::whereHas('business', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->get();
            return response()->json($slides);
        }
        // Si es admin, listar todos los slides
        return response()->json(Slide::with('business.owner')->get());
    }

    // Crear slide en un business
    public function store(StoreSlideRequest $request)
    {
        $user = Auth::user();
        $request->validated();
        $input = $request->all();
        $business = Business::findOrFail($input['business_id']);
        // Solo el owner del business o admin puede crear slides
        if (!$this->isAdmin($user) && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $slide = $business->slides()->create($input);
        return response()->json($slide, 201);
    }

    // Ver un slide específico
    public function show($id)
    {
        $slide = Slide::findOrFail($id);
        return response()->json($slide);
    }

    // Actualizar un slide
    public function update(UpdateSlideRequest $request, $id)
    {
        $user = Auth::user();
        $request->validated();
        $input = $request->all();
        $business = Business::findOrFail($input['business_id']);
        // Solo el owner del business o admin puede actualizar slides
        if (!$this->isAdmin($user) && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $slide = Slide::findOrFail($id);
        $slide->update($input);
        return response()->json($slide);
    }

    // Eliminar un slide
    public function destroy($businessId, $id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($businessId);
        // Solo el owner del business o admin puede eliminar slides
        if (!$this->isAdmin($user) && $business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $slide = $business->slides()->findOrFail($id);
        $slide->delete();
        return response()->json(['message' => 'Slide eliminado']);
    }
}
