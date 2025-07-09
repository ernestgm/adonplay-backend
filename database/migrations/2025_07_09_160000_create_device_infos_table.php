<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_infos', function (Blueprint $table) {
            $table->id();
            $table->boolean('overlay_permission')->default(false);
            $table->string('app_version')->nullable();
            $table->string('android_version')->nullable();
            $table->string('device_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_infos');
    }
};

