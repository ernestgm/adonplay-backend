<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $user = User::with('roles')->where('id', Auth::user()->id)->first();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json(['success' => 'success']);
    }

    public function index()
    {
        $users = [];
        $authUser = Auth::user();
        $isAdmin = $this->isAdmin($authUser);
        if ($isAdmin) {
            // Admin puede ver todos menos él mismo
            $users = User::with('roles')->where('id', '!=', $authUser->id)->get();
        }

        return response()->json($users);
    }

    // Mostrar un usuario específico
    public function show($id)
    {
        $authUser = Auth::user();
        $isAdmin = $this->isAdmin($authUser);
        if (!$isAdmin && $authUser->id != $id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $user = User::with('roles')->find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($user);
    }

    // Crear un nuevo usuario
    public function store(UserStoreRequest $request): JsonResponse
    {
        $authUser = Auth::user();
        $isAdmin = $this->isAdmin($authUser);
        if (!$isAdmin) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        // Validación de los datos del usuario
        $request->validated();
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->roles()->sync([$request->role]);
        $user->load('roles');
        $user->role = $user->roles->first() ? $user->roles->first()->name : null;
        return response()->json($user, 201);
    }

    // Actualizar un usuario existente
    public function update(UserUpdateRequest $request, $id): JsonResponse
    {
        $authUser = Auth::user();
        $isAdmin = $this->isAdmin($authUser);
        if (!$isAdmin && $authUser->id != $id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        $request->validated();
        $input = $request->all();
        if (isset($input['password'])) {
            if ($input['password'] === '' || $input['password'] === null) {
                unset($input['password']);
            } else {
                $input['password'] = Hash::make($input['password']);
            }
        }
        $user->update($input);
        if ($request->filled('role')) {
            $user->roles()->sync([$request->role]);
        }
        $user->load('roles');
        $user->role = $user->roles->first() ? $user->roles->first()->name : null;
        return response()->json($user);
    }

    // Eliminar uno o más usuarios
    public function destroy(Request $request): JsonResponse
    {
        $authUser = Auth::user();
        $isAdmin = $this->isAdmin($authUser);
        if (!$isAdmin) {
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
        $deleted = DB::table('users')->whereIn('id', $ids)->delete();
        return response()->json([
                'success' => true,
                'message' => "$deleted record(s) deleted."
            ]
        );
    }
}
