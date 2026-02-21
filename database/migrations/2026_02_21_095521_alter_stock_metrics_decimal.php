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
        Schema::table('stock_metrics', function (Blueprint $table) {
            $table->decimal('mos', 15, 2)->change();
            $table->decimal('target_price', 15, 2)->change();
            $table->decimal('target_r1', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_metrics', function (Blueprint $table) {
        });
    }
};
