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
        Schema::create('spare_approval_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performa_id')->constrained('spare_approval_performa');
            $table->foreignId('spare_id')->constrained('spares');
            $table->integer('quantity_requested');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_approval_items');
    }
};
