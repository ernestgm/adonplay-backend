<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        $users = User::with('roles')->get();
        return response()->json($users);
    }

    // Mostrar un usuario específico
    public function show($id)
    {
        $user = User::with('roles')->find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($user);
    }

    // Crear un nuevo usuario
    public function store(UserStoreRequest $request): JsonResponse
    {
        // Validación de los datos del usuario
        $request->validated();
        $input = $request->all();

        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        // Asignar rol
        $user->roles()->sync([$request->role]);
        $user->load('roles');
        $user->role = $user->roles->first() ? $user->roles->first()->name : null;

        return response()->json($user, 201);
    }

    // Actualizar un usuario existente
    public function update(UserUpdateRequest $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validated();


        $input = $request->all();
        if (isset($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        }

        $user->update($input);

        // Actualizar rol si viene en el request
        if ($request->filled('role')) {
            $user->roles()->sync([$request->role]);
        }
        $user->load('roles');
        $user->role = $user->roles->first() ? $user->roles->first()->name : null;

        return response()->json($user);
    }

    // Eliminar un usuario
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    }
}
