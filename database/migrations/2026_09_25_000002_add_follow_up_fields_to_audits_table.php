<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->string('follow_up_status')->default('not_required')->after('notes');
            $table->foreignId('follow_up_owner_id')->nullable()->after('follow_up_status')->constrained('users')->nullOnDelete();
            $table->dateTime('follow_up_due_at')->nullable()->after('follow_up_owner_id');
            $table->text('follow_up_notes')->nullable()->after('follow_up_due_at');

            $table->index(['follow_up_status', 'follow_up_due_at']);
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropForeign(['follow_up_owner_id']);
            $table->dropIndex(['follow_up_status', 'follow_up_due_at']);
            $table->dropColumn([
                'follow_up_status',
                'follow_up_owner_id',
                'follow_up_due_at',
                'follow_up_notes',
            ]);
        });
    }
};
