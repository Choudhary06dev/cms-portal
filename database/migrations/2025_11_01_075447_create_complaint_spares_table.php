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
            $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');
            $table->foreignId('spare_id')->constrained('spares')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->unsignedBigInteger('used_by')->nullable();
            $table->foreign('used_by')->references('id')->on('employees')->onDelete('set null');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
