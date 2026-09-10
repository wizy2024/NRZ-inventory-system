<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_passes', function (Blueprint $table) {
            $table->id();
            $table->string('pass_number')->unique();
            $table->foreignId('asset_id')->constrained()->restrictOnDelete();
            $table->foreignId('maintenance_log_id')->nullable()->constrained()->nullOnDelete();
            $table->string('collector_name');
            $table->string('collector_contact')->nullable();
            $table->string('collector_id_number')->nullable();
            $table->foreignId('issued_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('released_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_passes');
    }
};
