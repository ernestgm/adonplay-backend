<?php
// app/Http/Controllers/LoginCodeController.php
namespace App\Http\Controllers;

use App\Models\LoginCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LoginCodeController extends Controller
{
    // Generar código
    public function generate(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
        ]);

        $code = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

        $loginCode = LoginCode::create([
            'code' => $code,
            'device_id' => $request->device_id,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        return response()->json(['code' => $code]);
    }

    // Login con código
    public function login(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:8',
            'device_id' => 'required|string',
        ]);

        $loginCode = LoginCode::where('code', $request->code)
            ->where('device_id', $request->device_id)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$loginCode) {
            return response()->json(['message' => 'Código inválido o expirado'], 401);
        }

        $user = User::find($loginCode->user_id);
        if (!$user) {
            return response()->json(['message' => 'No existe el usuario'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        // Opcional: eliminar el código después de usarlo
        $loginCode->delete();

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function confirmCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:8',
        ]);

        $loginCode = LoginCode::where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$loginCode) {
            return response()->json(['message' => 'Código inválido, expirado o ya confirmado'], 400);
        }

        $loginCode->user_id = Auth::user()->id;
        $loginCode->save();

        return response()->json(['message' => 'Código confirmado']);
    }
}
