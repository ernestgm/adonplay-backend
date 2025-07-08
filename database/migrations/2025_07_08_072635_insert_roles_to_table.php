<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Administrator', 'code' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Owner', 'code' => 'owner', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
};
