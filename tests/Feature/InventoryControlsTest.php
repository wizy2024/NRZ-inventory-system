<?php

namespace Tests\Feature;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use App\Models\Audit;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryControlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_assignment_changes_are_preserved_as_history(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $department = Department::create(['name' => 'IT']);
        $newDepartment = Department::create(['name' => 'Finance']);
        $location = Location::create(['name' => 'Harare', 'code' => 'HAR']);
        $newLocation = Location::create(['name' => 'Bulawayo', 'code' => 'BYO']);
        $assignee = User::factory()->create();

        $asset = Asset::create([
            'asset_tag' => 'HISTORY-001',
            'serial_number' => 'HISTORY-SERIAL-001',
            'type' => 'Laptop',
            'brand' => 'Test',
            'department_id' => $department->id,
            'location_id' => $location->id,
            'status' => 'active',
        ]);

        $asset->update([
            'department_id' => $newDepartment->id,
            'location_id' => $newLocation->id,
            'assigned_to_user_id' => $assignee->id,
            'assigned_at' => now(),
        ]);

        $this->assertCount(2, $asset->assignmentHistories()->get());
        $this->assertDatabaseHas('asset_assignment_histories', [
            'asset_id' => $asset->id,
            'department_id' => $newDepartment->id,
            'location_id' => $newLocation->id,
            'assigned_to_user_id' => $assignee->id,
            'changed_by' => $user->id,
            'action' => 'updated',
        ]);
    }

    public function test_exception_audits_open_follow_up_by_default(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $asset = Asset::create([
            'asset_tag' => 'AUDIT-001',
            'serial_number' => 'AUDIT-SERIAL-001',
            'type' => 'Laptop',
            'brand' => 'Test',
            'department_id' => Department::create(['name' => 'IT'])->id,
            'location_id' => Location::create(['name' => 'Harare', 'code' => 'HAR'])->id,
            'status' => 'active',
        ]);

        $audit = Audit::create([
            'asset_id' => $asset->id,
            'result' => 'missing',
            'checked_at' => now(),
        ]);

        $this->assertSame('open', $audit->follow_up_status);
        $this->assertSame($user->id, $audit->audited_by);
    }

    public function test_asset_deletion_is_disabled_in_the_inventory_resource(): void
    {
        $this->assertFalse(AssetResource::canDelete(new Asset));
        $this->assertFalse(AssetResource::canDeleteAny());
    }
}
