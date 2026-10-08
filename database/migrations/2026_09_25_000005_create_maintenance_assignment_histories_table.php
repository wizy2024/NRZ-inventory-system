<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_assignment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_log_id')->constrained('maintenance_logs')->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->dateTime('effective_at');
            $table->timestamps();

            $table->index(['maintenance_log_id', 'effective_at'], 'maintenance_assignment_history_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_assignment_histories');
    }
};
