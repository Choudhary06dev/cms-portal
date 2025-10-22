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
        Schema::table('spares', function (Blueprint $table) {
            // First, update existing data to match new enum values
            DB::statement("UPDATE spares SET category = 'electrical' WHERE category = 'electric'");
            DB::statement("UPDATE spares SET category = 'plumbing' WHERE category = 'sanitary'");
            
            // Then modify the enum
            $table->enum('category', ['electrical', 'plumbing', 'kitchen', 'general', 'tools', 'consumables'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spares', function (Blueprint $table) {
            // Revert data back to old enum values
            DB::statement("UPDATE spares SET category = 'electric' WHERE category = 'electrical'");
            DB::statement("UPDATE spares SET category = 'sanitary' WHERE category = 'plumbing'");
            
            // Revert the enum
            $table->enum('category', ['electric', 'sanitary', 'kitchen', 'general'])->change();
        });
    }
};
