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
        Schema::create('spares', function (Blueprint $table) {
            $table->id();
            $table->string('item_name', 150);
            $table->enum('category', ['technical', 'service', 'billing', 'sanitary', 'electric', 'kitchen', 'plumbing', 'other']);
            $table->string('unit', 50)->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('threshold_level')->default(10);
            $table->string('supplier', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
        });

        Schema::create('spare_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spare_id')->constrained('spares');
            $table->enum('change_type', ['in', 'out']);
            $table->integer('quantity');
            $table->integer('reference_id')->nullable(); // complaint_id or purchase_id
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_stock_logs');
        Schema::dropIfExists('spares');
    }
};
