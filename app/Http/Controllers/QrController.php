<?php

namespace App\Http\Controllers;

use App\Models\Qr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreQrRequest;
use App\Http\Requests\UpdateQrRequest;

class QrController extends Controller
{
    // Listar Qr del usuario autenticado o todos si es admin
    public function index()
    {
        $user = Auth::user();
        if ($this->isAdmin($user)) {
            return response()->json(Qr::all());
        }
        return response()->json($user->qrs);
    }

    // Crear un nuevo Qr
    public function store(StoreQrRequest $request)
    {
        $user = Auth::user();
        $qr = $user->qrs()->create($request->validated());
        return response()->json($qr, 201);
    }

    // Ver un Qr específico
    public function show($id)
    {
        $user = Auth::user();
        $qr = Qr::findOrFail($id);
        if (!$this->isAdmin($user) && $qr->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json($qr);
    }

    // Actualizar un Qr
    public function update(UpdateQrRequest $request, $id)
    {
        $user = Auth::user();
        $qr = Qr::findOrFail($id);
        if (!$this->isAdmin($user) && $qr->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $qr->update($request->validated());
        return response()->json($qr);
    }

    // Eliminar un Qr
    public function destroy($id)
    {
        $user = Auth::user();
        $qr = Qr::findOrFail($id);
        if (!$this->isAdmin($user) && $qr->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $qr->delete();
        return response()->json(['message' => 'QR eliminado']);
    }
}

