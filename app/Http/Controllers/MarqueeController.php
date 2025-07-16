<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Marquee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMarqueeRequest;
use App\Http\Requests\UpdateMarqueeRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MarqueeController extends Controller
{
    // Listar marquees del business o todos si es admin
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($this->isAdmin($user)) {
            return response()->json(Marquee::with('business.owner')->get());
        }

        $marquees = Marquee::whereHas('business', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->with('business.owner')->get();

        return response()->json($marquees);
    }

    // Crear un nuevo marquee para un business
    public function store(StoreMarqueeRequest $request)
    {
        $user = Auth::user();
        $businessId = $request->get('business_id');
        if ($this->isAdmin($user)) {
            $business = Business::findOrFail($businessId);
        } else {
            $business = $user->businesses()->findOrFail($businessId);
        }

        $marquee = $business->marquees()->create($request->validated());
        return response()->json($marquee, 201);
    }

    // Ver un marquee específico
    public function show($id)
    {
        $user = Auth::user();
        $marquee = Marquee::findOrFail($id);
        if (!$this->isAdmin($user) && $marquee->business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json($marquee);
    }

    // Actualizar un marquee
    public function update(UpdateMarqueeRequest $request, $id)
    {
        $user = Auth::user();
        $marquee = Marquee::findOrFail($id);
        if (!$this->isAdmin($user) && $marquee->business->owner_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $marquee->update($request->validated());
        return response()->json($marquee);
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
        $deleted = DB::table('marquees')->whereIn('id', $ids)->delete();
        return response()->json([
                'success' => true,
                'message' => "$deleted record(s) deleted."
            ]
        );
    }
}
