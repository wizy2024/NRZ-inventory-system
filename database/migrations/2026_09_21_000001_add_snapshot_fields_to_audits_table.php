<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->string('expected_location')->nullable()->after('checked_at');
            $table->string('observed_location')->nullable()->after('expected_location');
            $table->string('expected_assignee')->nullable()->after('observed_location');
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropColumn([
                'expected_location',
                'observed_location',
                'expected_assignee',
            ]);
        });
    }
};
