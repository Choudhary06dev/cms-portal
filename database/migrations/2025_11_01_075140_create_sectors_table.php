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
        Schema::create('sectors', function (Blueprint $table) {
            $table->id();
            // Add city_id column (nullable for existing installations)
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
        
        // Add foreign key constraint only if cities table exists
        if (Schema::hasTable('cities')) {
            Schema::table('sectors', function (Blueprint $table) {
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sectors');
    }
};
