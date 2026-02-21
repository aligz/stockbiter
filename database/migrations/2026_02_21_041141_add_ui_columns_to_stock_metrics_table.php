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
        Schema::table('stock_metrics', function (Blueprint $table) {
            $table->decimal('target_r1', 15, 2)->nullable()->after('mos');
            $table->decimal('day_high', 15, 2)->nullable()->after('target_r1');
            $table->string('bandar_status')->nullable()->after('bandar_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_metrics', function (Blueprint $table) {
            $table->dropColumn(['target_r1', 'day_high', 'bandar_status']);
        });
    }
};
