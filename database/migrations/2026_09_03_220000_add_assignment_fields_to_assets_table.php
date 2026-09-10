<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('assigned_to_user_id')->nullable()->after('location_id')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->after('assigned_to_user_id');
            $table->text('assignment_notes')->nullable()->after('assigned_at');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_user_id']);
            $table->dropColumn(['assigned_to_user_id', 'assigned_at', 'assignment_notes']);
        });
    }
};
