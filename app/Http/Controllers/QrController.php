<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Qr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreQrRequest;
use App\Http\Requests\UpdateQrRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QrController extends Controller
{
    // Listar Qr del business o todos si es admin
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($this->isAdmin($user)) {
            return response()->json(Qr::with('business.owner')->get());
        }
        $businessId = $request->get('business_id');
        $business = $user->businesses()->findOrFail($businessId);

        $qrs = Qr::whereHas('business', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->with('business.owner')->get();

        return response()->json($qrs);
    }

    // Crear un nuevo Qr para un business
    public function store(StoreQrRequest $request)
    {
        $user = Auth::user();
        $businessId = $request->get('business_id');
        if ($this->isAdmin($user)) {
            $business = Business::findOrFail($businessId);
        } else {
            $business = $user->businesses()->findOrFail($businessId);
        }

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

    // Eliminar un marquee
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        $validator = Validator::make(['ids' => $ids], [
            'ids' => 'required|array',
            'ids.*' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], app('VALIDATION_STATUS'));
        }
        $deleted = DB::table('qrs')->whereIn('id', $ids)->delete();
        return response()->json([
                'success' => true,
                'message' => "$deleted record(s) deleted."
            ]
        );
    }
}
