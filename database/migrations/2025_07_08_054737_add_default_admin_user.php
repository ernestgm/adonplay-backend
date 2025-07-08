<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'ernestgm2006@gmail.com',
            'password' => Hash::make('Admin.2025'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'ernestgm2006@gmail.com')->delete();
    }
};
