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
        if (Schema::hasTable('spare_stock_logs') && !Schema::hasColumn('spare_stock_logs', 'brand_name')) {
            Schema::table('spare_stock_logs', function (Blueprint $table) {
                $table->string('brand_name', 100)->nullable()->after('spare_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('spare_stock_logs') && Schema::hasColumn('spare_stock_logs', 'brand_name')) {
            Schema::table('spare_stock_logs', function (Blueprint $table) {
                $table->dropColumn('brand_name');
            });
        }
    }
};
