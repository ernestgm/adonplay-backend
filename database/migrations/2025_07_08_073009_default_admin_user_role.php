<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buscar usuario Admin y rol admin
        $user = DB::table('users')->where('email', 'ernestgm2006@gmail.com')->first();
        $role = DB::table('roles')->where('code', 'admin')->first();

        // Si ambos existen, asignar el rol
        if ($user && $role) {
            DB::table('role_user')->insert([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Remover la relación si existe
        $user = DB::table('users')->where('email', 'ernestgm2006@gmail.com')->first();
        $role = DB::table('roles')->where('code', 'admin')->first();

        if ($user && $role) {
            DB::table('role_user')
                ->where('user_id', $user->id)
                ->where('role_id', $role->id)
                ->delete();
        }
    }
};
