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
    Schema::create('status_alerts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
        $table->string('previous_status');
        $table->string('new_status');
        $table->text('message')->nullable();
        $table->boolean('is_read')->default(false);
        $table->timestamp('triggered_at');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_alerts');
    }
};
