<?php

namespace App\Http\Controllers;

use App\Models\Marquee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMarqueeRequest;
use App\Http\Requests\UpdateMarqueeRequest;

class MarqueeController extends Controller
{
    // Listar marquees del usuario autenticado o todos si es admin
    public function index()
    {
        $user = Auth::user();
        if ($this->isAdmin($user)) {
            return response()->json(Marquee::all());
        }
        return response()->json($user->marquees);
    }

    // Crear un nuevo marquee
    public function store(StoreMarqueeRequest $request)
    {
        $user = Auth::user();
        $marquee = $user->marquees()->create($request->validated());
        return response()->json($marquee, 201);
    }

    // Ver un marquee específico
    public function show($id)
    {
        $user = Auth::user();
        $marquee = Marquee::findOrFail($id);
        if (!$this->isAdmin($user) && $marquee->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json($marquee);
    }

    // Actualizar un marquee
    public function update(UpdateMarqueeRequest $request, $id)
    {
        $user = Auth::user();
        $marquee = Marquee::findOrFail($id);
        if (!$this->isAdmin($user) && $marquee->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $marquee->update($request->validated());
        return response()->json($marquee);
    }

    // Eliminar un marquee
    public function destroy($id)
    {
        $user = Auth::user();
        $marquee = Marquee::findOrFail($id);
        if (!$this->isAdmin($user) && $marquee->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $marquee->delete();
        return response()->json(['message' => 'Marquee eliminado']);
    }
}
