<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Department;
use App\Models\Location;
use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MaintenanceAlertsTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_technician_receives_an_alert_for_new_maintenance_work(): void
    {
        $technician = User::factory()->create();
        Role::create(['name' => 'Technician', 'guard_name' => 'web']);
        $technician->assignRole('Technician');
        $asset = $this->createAsset();

        MaintenanceLog::create([
            'asset_id' => $asset->id,
            'technician_id' => $technician->id,
            'symptom' => 'Device will not start',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $technician->id,
            'notifiable_type' => User::class,
        ]);
        $this->assertSame(1, $technician->notifications()->count());
    }

    public function test_description_edits_do_not_create_duplicate_alerts(): void
    {
        $technician = User::factory()->create();
        $asset = $this->createAsset();
        $maintenanceLog = MaintenanceLog::create([
            'asset_id' => $asset->id,
            'technician_id' => $technician->id,
            'symptom' => 'Keyboard fault',
            'status' => 'pending',
        ]);

        $maintenanceLog->update(['description' => 'Additional details added.']);

        $this->assertSame(1, $technician->notifications()->count());
    }

    public function test_moving_work_into_progress_alerts_the_assigned_technician(): void
    {
        $technician = User::factory()->create();
        $asset = $this->createAsset();
        $maintenanceLog = MaintenanceLog::create([
            'asset_id' => $asset->id,
            'technician_id' => $technician->id,
            'symptom' => 'Screen fault',
            'status' => 'pending',
        ]);

        $maintenanceLog->update(['status' => 'in_progress']);

        $this->assertSame(2, $technician->notifications()->count());
    }

    public function test_transferring_work_notifies_the_new_technician_and_records_history(): void
    {
        $firstTechnician = User::factory()->create();
        $newTechnician = User::factory()->create();
        $asset = $this->createAsset();
        $maintenanceLog = MaintenanceLog::create([
            'asset_id' => $asset->id,
            'technician_id' => $firstTechnician->id,
            'symptom' => 'Network fault',
            'status' => 'in_progress',
        ]);

        $maintenanceLog->update(['technician_id' => $newTechnician->id]);

        $this->assertSame(1, $firstTechnician->notifications()->count());
        $this->assertSame(1, $newTechnician->notifications()->count());
        $this->assertDatabaseHas('maintenance_assignment_histories', [
            'maintenance_log_id' => $maintenanceLog->id,
            'technician_id' => $firstTechnician->id,
            'action' => 'created',
        ]);
        $this->assertDatabaseHas('maintenance_assignment_histories', [
            'maintenance_log_id' => $maintenanceLog->id,
            'technician_id' => $newTechnician->id,
            'action' => 'transferred',
        ]);
    }

    private function createAsset(): Asset
    {
        return Asset::create([
            'asset_tag' => 'ALERT-' . uniqid(),
            'serial_number' => 'ALERT-SERIAL-' . uniqid(),
            'type' => 'Laptop',
            'brand' => 'Test',
            'department_id' => Department::create(['name' => 'IT-' . uniqid()])->id,
            'location_id' => Location::create(['name' => 'Harare-' . uniqid(), 'code' => strtoupper(substr(uniqid(), 0, 3))])->id,
            'status' => 'active',
        ]);
    }
}
