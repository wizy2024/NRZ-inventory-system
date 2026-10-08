<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->index(['status', 'location_id']);
            $table->index(['department_id', 'assigned_to_user_id']);
            $table->index('warranty_expiry');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['status', 'location_id']);
            $table->dropIndex(['department_id', 'assigned_to_user_id']);
            $table->dropIndex(['warranty_expiry']);
        });
    }
};
