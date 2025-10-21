<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('role_name', 50)->unique();
                $table->string('description', 255)->nullable();
                $table->timestamps();
            });

            // Insert default roles
            DB::table('roles')->insert([
                [
                    'role_name' => 'admin',
                    'description' => 'System Administrator',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'role_name' => 'manager',
                    'description' => 'Manager',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'role_name' => 'employee',
                    'description' => 'Employee',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'role_name' => 'client',
                    'description' => 'Client',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
