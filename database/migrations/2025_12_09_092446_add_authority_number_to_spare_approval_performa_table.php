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
        Schema::table('spare_approval_performa', function (Blueprint $table) {
            $table->string('authority_number')->nullable()->after('performa_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spare_approval_performa', function (Blueprint $table) {
            $table->dropColumn('authority_number');
        });
    }
};
