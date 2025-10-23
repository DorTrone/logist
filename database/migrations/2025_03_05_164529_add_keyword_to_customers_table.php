<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->text('ext_keyword')->nullable();
        });

        DB::statement("CREATE INDEX customers_ext_keyword_gin ON customers USING GIN (to_tsvector('simple', ext_keyword))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS customers_ext_keyword_gin");

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('ext_keyword');
        });
    }
};
