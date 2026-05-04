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
    Schema::create('machine_readings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
        $table->string('sensor_key');
        $table->decimal('value', 10, 2);
        $table->string('unit')->nullable();
        $table->timestamp('read_at');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_readings');
    }
};
