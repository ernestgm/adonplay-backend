<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('marquees', function (Blueprint $table) {
            $table->unsignedBigInteger('business_id')->after('id');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('marquees', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id');
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });
    }
};

