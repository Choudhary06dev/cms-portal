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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('client_id')->constrained('clients');
            $table->string('category', 100);
            $table->string('department', 100)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['new', 'assigned', 'in_progress', 'resolved', 'closed'])->default('new');
            $table->unsignedBigInteger('assigned_employee_id')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent', 'emergency'])->default('medium');
            $table->timestamp('closed_at')->nullable();
            
            // Spare part columns
            $table->foreignId('spare_id')->nullable()->constrained('spares');
            $table->integer('spare_quantity')->nullable();
            $table->unsignedBigInteger('spare_used_by')->nullable();
            $table->timestamp('spare_used_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
