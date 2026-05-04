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
    Schema::create('service_orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
        $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('created_by')->constrained('users');
        $table->enum('type', ['preventive', 'corrective']);
        $table->enum('status', ['open', 'in_progress', 'completed', 'cancelled'])->default('open');
        $table->timestamp('started_at')->nullable();
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
