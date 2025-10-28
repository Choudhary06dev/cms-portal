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
        // Add deleted_at column to all tables that need soft deletes
        $tables = [
            'roles',
            'users', 
            'role_permissions',
            'clients',
            'employees',
            'employee_leaves',
            'complaints',
            'complaint_attachments',
            'complaint_logs',
            'complaint_spares',
            'spares',
            'spare_approval_performa',
            'spare_approval_items',
            'spare_stock_logs',
            'reports_summary',
            'sla_rules'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove deleted_at column from all tables
        $tables = [
            'roles',
            'users', 
            'role_permissions',
            'clients',
            'employees',
            'employee_leaves',
            'complaints',
            'complaint_attachments',
            'complaint_logs',
            'complaint_spares',
            'spares',
            'spare_approval_performa',
            'spare_approval_items',
            'spare_stock_logs',
            'reports_summary',
            'sla_rules'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
