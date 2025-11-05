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
        Schema::create('stock_add_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spare_id')->constrained('spares')->onDelete('cascade');
            $table->date('add_date')->comment('Date when stock was added');
            $table->string('category', 100)->comment('Product category');
            $table->string('product_name', 150)->comment('Product name');
            $table->integer('quantity_added')->comment('Quantity added to stock');
            $table->integer('available_stock_after')->comment('Available stock after adding');
            $table->text('remarks')->nullable()->comment('Remarks for stock addition');
            $table->unsignedBigInteger('added_by')->nullable()->comment('User/Employee who added stock');
            $table->integer('reference_id')->nullable()->comment('Reference ID (purchase_id, etc.)');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('spare_id');
            $table->index('add_date');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_add_data');
    }
};
