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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('image_tm')->nullable();
            $table->string('image_ru')->nullable();
            $table->string('image_cn')->nullable();
            $table->string('image_2')->nullable();
            $table->string('image_2_tm')->nullable();
            $table->string('image_2_ru')->nullable();
            $table->string('image_2_cn')->nullable();
            $table->dateTime('datetime_start')->useCurrent();
            $table->dateTime('datetime_end')->useCurrent();
            $table->string('url')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
