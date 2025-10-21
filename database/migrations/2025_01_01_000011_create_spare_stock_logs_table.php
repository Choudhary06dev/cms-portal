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
        Schema::create('spare_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spare_id')->constrained('spares');
            $table->enum('change_type', ['in', 'out']);
            $table->integer('quantity');
            $table->integer('reference_id')->nullable(); // complaint_id or purchase_id
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_stock_logs');
    }
};
