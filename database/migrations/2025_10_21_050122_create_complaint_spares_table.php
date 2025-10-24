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
        Schema::create('complaint_spares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints');
            $table->foreignId('spare_id')->constrained('spares');
            $table->integer('quantity');
            $table->unsignedBigInteger('used_by');
            $table->timestamp('used_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_spares');
    }
};
