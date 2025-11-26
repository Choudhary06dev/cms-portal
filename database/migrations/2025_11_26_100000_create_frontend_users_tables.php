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
        // Create frontend_users table
        Schema::create('frontend_users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('name', 100)->nullable();
            $table->string('password', 255);
            $table->rememberToken();
            $table->string('email', 150)->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create frontend_user_locations table (pivot table for cities and sectors assignments)
        Schema::create('frontend_user_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('frontend_user_id');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('sector_id')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('frontend_user_id')->references('id')->on('frontend_users')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('sector_id')->references('id')->on('sectors')->onDelete('cascade');

            // Ensure unique combinations
            $table->unique(['frontend_user_id', 'city_id', 'sector_id'], 'feu_locations_unique');
        });

        // Create frontend_user_cmes table (privileges)
        Schema::create('frontend_user_cmes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('frontend_user_id');
            $table->unsignedBigInteger('cme_id');
            $table->timestamps();

            $table->foreign('frontend_user_id')->references('id')->on('frontend_users')->onDelete('cascade');
            $table->foreign('cme_id')->references('id')->on('cmes')->onDelete('cascade');
        });

        // Create frontend_user_cities table (privileges)
        Schema::create('frontend_user_cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('frontend_user_id');
            $table->unsignedBigInteger('city_id');
            $table->timestamps();

            $table->foreign('frontend_user_id')->references('id')->on('frontend_users')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frontend_user_cities');
        Schema::dropIfExists('frontend_user_cmes');
        Schema::dropIfExists('frontend_user_locations');
        Schema::dropIfExists('frontend_users');
    }
};
