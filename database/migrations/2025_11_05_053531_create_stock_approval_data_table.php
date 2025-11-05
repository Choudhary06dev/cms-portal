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
        Schema::create('stock_approval_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spare_id')->constrained('spares')->onDelete('cascade');
            $table->unsignedBigInteger('complaint_id')->nullable()->comment('Complaint ID');
            $table->unsignedBigInteger('approval_id')->nullable()->comment('Approval ID (required for records from approval modal)');
            $table->date('issue_date')->comment('Date when stock was issued');
            $table->string('category', 100)->comment('Product category');
            $table->string('product_name', 150)->comment('Product name');
            $table->integer('available_stock')->default(0)->comment('Available stock at time of issue');
            $table->integer('requested_stock')->default(0)->comment('Requested stock quantity');
            $table->integer('approval_stock')->default(0)->comment('Approved stock quantity');
            $table->integer('issued_quantity')->default(0)->comment('Actual quantity issued');
            $table->enum('status', ['pending', 'approved', 'rejected', 'request_for_stock'])->default('pending')->comment('Approval status');
            $table->text('remarks')->nullable()->comment('Remarks for stock issue');
            $table->unsignedBigInteger('issued_by')->nullable()->comment('User/Employee who issued stock');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('User/Employee who approved');
            $table->timestamp('approved_at')->nullable()->comment('Approval date/time');
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
            $table->foreign('approval_id')->references('id')->on('spare_approval_performa')->onDelete('cascade');
            
            // Indexes
            $table->index('spare_id');
            $table->index('complaint_id');
            $table->index('approval_id');
            $table->index('issue_date');
            $table->index('category');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_approval_data');
    }
};
