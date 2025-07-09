<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slide_id');
            $table->enum('type', ['image', 'video']);
            $table->string('file_path');
            $table->string('audio_path')->nullable(); // Solo para imágenes
            $table->text('description')->nullable();
            $table->string('description_position')->nullable();
            $table->string('description_size')->nullable();
            $table->text('qr_info')->nullable();
            $table->string('qr_position')->nullable();
            $table->integer('duration')->default(5);
            $table->timestamps();

            $table->foreign('slide_id')->references('id')->on('slides')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
