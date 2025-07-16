<?php

namespace App\Http\Controllers;

use App\Models\Qr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreQrRequest;
use App\Http\Requests\UpdateQrRequest;
use Illuminate\Http\Request;

class QrController extends Controller
{
    // Listar Qr del business o todos si es admin
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($this->isAdmin($user)) {
            return response()->json(Qr::all());
        }
        $businessId = $request->get('business_id');
        $business = $user->businesses()->findOrFail($businessId);
        return response()->json($business->qrs);
    }

    // Crear un nuevo Qr para un business
    public function store(StoreQrRequest $request)
    {
        $user = Auth::user();
        $businessId = $request->get('business_id');
        $business = $user->businesses()->findOrFail($businessId);
        $qr = $business->qrs()->create($request->validated());
        return response()->json($qr, 201);
    }

    // Ver un Qr específico
    public function show($id)
    {
        $user = Auth::user();
        $qr = Qr::findOrFail($id);
        if (!$this->isAdmin($user) && $qr->business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json($qr);
    }

    // Actualizar un Qr
    public function update(UpdateQrRequest $request, $id)
    {
        $user = Auth::user();
        $qr = Qr::findOrFail($id);
        if (!$this->isAdmin($user) && $qr->business->owner_id !== $user->id) {
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
        if (!$this->isAdmin($user) && $qr->business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $qr->delete();
        return response()->json(['message' => 'QR eliminado']);
    }
}
