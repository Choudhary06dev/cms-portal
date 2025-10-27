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
            $table->unsignedBigInteger('performa_id');
            $table->unsignedBigInteger('spare_id');
            $table->integer('quantity_requested');
            $table->text('reason')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('performa_id')->references('id')->on('spare_approval_performa')->onDelete('cascade');
            $table->foreign('spare_id')->references('id')->on('spares')->onDelete('cascade');

            // Indexes for better performance
            $table->index(['performa_id', 'spare_id']);
            $table->index('spare_id');
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
