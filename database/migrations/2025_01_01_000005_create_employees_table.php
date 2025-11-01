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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->nullable()->unique();
            $table->string('department', 100)->nullable();
            $table->string('designation', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('biometric_id', 50)->nullable()->unique();
            $table->date('date_of_hire')->nullable();
            $table->integer('leave_quota')->default(30);
            $table->text('address')->nullable();
            // Add city_id and sector_id columns (nullable for existing installations)
            $table->unsignedBigInteger('city_id')->nullable()->after('address');
            $table->unsignedBigInteger('sector_id')->nullable()->after('city_id');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
        
        // Add foreign key constraints only if cities and sectors tables exist
        if (Schema::hasTable('cities')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            });
        }
        if (Schema::hasTable('sectors')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreign('sector_id')->references('id')->on('sectors')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
