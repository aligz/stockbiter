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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->unique();
            $table->string('company_name')->nullable();
            $table->string('sector')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stocks')->cascadeOnDelete();
            $table->date('date');

            // Market Data
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('change', 10, 2)->nullable();
            $table->decimal('change_percentage', 8, 2)->nullable();
            $table->bigInteger('market_cap')->nullable();
            $table->bigInteger('volume')->nullable();

            // Adimology Data
            $table->string('bandar_code')->nullable();
            $table->decimal('bandar_avg_price', 15, 2)->nullable();
            $table->bigInteger('bandar_volume')->nullable();
            $table->bigInteger('total_bid_volume')->nullable();
            $table->bigInteger('total_offer_volume')->nullable();
            $table->decimal('offer_highest', 15, 2)->nullable(); // ARA proxy
            $table->decimal('bid_lowest', 15, 2)->nullable(); // ARB proxy
            $table->integer('fraksi')->nullable();
            $table->decimal('target_price', 15, 2)->nullable();
            $table->string('target_action')->nullable();
            $table->decimal('mos', 8, 2)->nullable();

            $table->timestamps();

            // Prevent duplicate daily entries for the same stock
            $table->unique(['stock_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_metrics');
        Schema::dropIfExists('stocks');
    }
};
