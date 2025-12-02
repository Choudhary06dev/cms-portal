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
        DB::statement("ALTER TABLE complaints MODIFY COLUMN status ENUM('new', 'assigned', 'in_progress', 'resolved', 'closed', 'work_performa', 'maint_performa', 'work_priced_performa', 'maint_priced_performa', 'product_na', 'un_authorized', 'pertains_to_ge_const_isld', 'barak_damages') NOT NULL DEFAULT 'new'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // CAUTION: This will fail if there are any records with 'barak_damages' status
        DB::statement("ALTER TABLE complaints MODIFY COLUMN status ENUM('new', 'assigned', 'in_progress', 'resolved', 'closed', 'work_performa', 'maint_performa', 'work_priced_performa', 'maint_priced_performa', 'product_na', 'un_authorized', 'pertains_to_ge_const_isld') NOT NULL DEFAULT 'new'");
    }
};
