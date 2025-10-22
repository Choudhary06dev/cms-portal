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
        Schema::table('complaints', function (Blueprint $table) {
            // Add title field
            $table->string('title')->after('id');
            
            // Add category field (rename from complaint_type)
            $table->enum('category', ['technical', 'service', 'billing', 'other'])->after('title');
            
            // Update priority to include urgent
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->change();
            
            // Rename assigned_to to assigned_employee_id
            $table->renameColumn('assigned_to', 'assigned_employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Remove added fields
            $table->dropColumn(['title', 'category']);
            
            // Revert priority
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->change();
            
            // Rename back
            $table->renameColumn('assigned_employee_id', 'assigned_to');
        });
    }
};
