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
        Schema::table('sla_rules', function (Blueprint $table) {
            $table->integer('max_resolution_time')->nullable()->after('max_response_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sla_rules', function (Blueprint $table) {
            $table->dropColumn('max_resolution_time');
        });
    }
};
