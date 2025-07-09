<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('device_id')->unique();
            $table->boolean('portrait')->default(false);
            $table->boolean('as_presentation')->default(false);
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('slide_id')->nullable();
            $table->unsignedBigInteger('marquee_id')->nullable();
            $table->unsignedBigInteger('qr_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('slide_id')->references('id')->on('slides')->onDelete('set null');
            $table->foreign('marquee_id')->references('id')->on('marquees')->onDelete('set null');
            $table->foreign('qr_id')->references('id')->on('qrs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};

