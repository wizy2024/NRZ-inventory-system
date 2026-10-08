<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('assigned_to_name')->nullable()->after('assigned_to_user_id');
        });

        Schema::table('asset_assignment_histories', function (Blueprint $table) {
            $table->string('assigned_to_name')->nullable()->after('assigned_to_user_id');
        });

        DB::table('assets')
            ->join('users', 'assets.assigned_to_user_id', '=', 'users.id')
            ->select('assets.id as asset_id', 'users.name as assignee_name')
            ->orderBy('assets.id')
            ->chunk(100, function ($assets): void {
                foreach ($assets as $asset) {
                    DB::table('assets')->where('id', $asset->asset_id)->update([
                        'assigned_to_name' => $asset->assignee_name,
                    ]);
                }
            });

        DB::table('asset_assignment_histories')
            ->join('users', 'asset_assignment_histories.assigned_to_user_id', '=', 'users.id')
            ->select('asset_assignment_histories.id as history_id', 'users.name as assignee_name')
            ->orderBy('asset_assignment_histories.id')
            ->chunk(100, function ($histories): void {
                foreach ($histories as $history) {
                    DB::table('asset_assignment_histories')->where('id', $history->history_id)->update([
                        'assigned_to_name' => $history->assignee_name,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('asset_assignment_histories', function (Blueprint $table) {
            $table->dropColumn('assigned_to_name');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('assigned_to_name');
        });
    }
};
