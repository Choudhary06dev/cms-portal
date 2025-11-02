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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('password', 255);
            $table->rememberToken();
            $table->string('email', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->unsignedBigInteger('city_id')->nullable()->after('role_id');
            $table->unsignedBigInteger('sector_id')->nullable()->after('city_id');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('theme', 10)->default('auto');
            $table->timestamps();
            
            // Add foreign key constraints only if cities and sectors tables exist
            if (Schema::hasTable('cities')) {
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            }
            if (Schema::hasTable('sectors')) {
                $table->foreign('sector_id')->references('id')->on('sectors')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
