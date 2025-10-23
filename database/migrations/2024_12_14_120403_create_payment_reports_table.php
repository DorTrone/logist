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
        Schema::create('payment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('paid_by');
            $table->unsignedTinyInteger('payment')->default(0);
            $table->double('price_cny')->nullable();
            $table->double('price_usd')->nullable();
            $table->double('price_tmt')->nullable();
            $table->text('note')->nullable();
            $table->json('images')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_reports');
    }
};
