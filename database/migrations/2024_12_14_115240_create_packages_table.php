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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('barcode')->index();
            $table->double('weight')->default(0);
            $table->double('weight_price')->default(0);
            $table->double('total_price')->default(0);
            $table->text('note')->nullable();
            $table->json('images')->nullable();
            $table->unsignedTinyInteger('location')->default(0);
            $table->unsignedTinyInteger('type')->default(0);
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedTinyInteger('payment_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
