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
    Schema::create('maintenance_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
        $table->foreignId('service_order_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->text('action');
        $table->string('defect_type')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
