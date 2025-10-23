<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('surname');
            $table->string('username')->unique();
            $table->string('password');
            $table->text('note')->nullable();
            $table->dateTime('last_seen')->nullable();
            $table->unsignedTinyInteger('auth_method')->default(0);
            $table->unsignedTinyInteger('language')->default(0);
            $table->unsignedTinyInteger('platform')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
