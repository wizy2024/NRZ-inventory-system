<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique();
            $table->string('serial_number')->unique();
            $table->string('mac_address')->nullable()->unique();
            $table->string('type'); // Desktop, Laptop, Server, etc.
            $table->string('brand');
            $table->json('specs')->nullable(); // store specs as JSON
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->string('status')->default('active'); // active, decommissioned, etc.
            $table->text('condemnation_reason')->nullable();
            $table->date('condemned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
