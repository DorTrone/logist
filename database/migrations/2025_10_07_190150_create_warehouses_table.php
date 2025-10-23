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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Название склада (обязательное поле!)
            $table->string('phone')->nullable();  // Телефон
            $table->text('address')->nullable();  // Адрес
            $table->string('postal_code')->nullable();  // Почтовый индекс
            $table->json('meta')->nullable();  // Дополнительные данные (JSON)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
