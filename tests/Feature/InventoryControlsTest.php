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

    public function test_asset_can_be_assigned_to_a_person_without_a_system_account(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $department = Department::create(['name' => 'IT']);
        $location = Location::create(['name' => 'Harare', 'code' => 'HAR']);

        $asset = Asset::create([
            'asset_tag' => 'FREE-TEXT-001',
            'serial_number' => 'FREE-TEXT-SERIAL-001',
            'type' => 'Desktop',
            'brand' => 'Test',
            'department_id' => $department->id,
            'location_id' => $location->id,
            'assigned_to_name' => 'External Staff Member',
            'status' => 'active',
        ]);

        $this->assertNull($asset->assigned_to_user_id);
        $this->assertSame('External Staff Member', $asset->assignee_name);
        $this->assertDatabaseHas('asset_assignment_histories', [
            'asset_id' => $asset->id,
            'assigned_to_user_id' => null,
            'assigned_to_name' => 'External Staff Member',
        ]);
    }

    public function test_changing_to_a_free_text_assignee_clears_a_legacy_user_assignment(): void
    {
        $this->actingAs(User::factory()->create());
        $assignee = User::factory()->create();
        $department = Department::create(['name' => 'IT']);
        $location = Location::create(['name' => 'Harare', 'code' => 'HAR']);

        $asset = Asset::create([
            'asset_tag' => 'LEGACY-ASSIGNEE-001',
            'serial_number' => 'LEGACY-ASSIGNEE-SERIAL-001',
            'type' => 'Desktop',
            'brand' => 'Test',
            'department_id' => $department->id,
            'location_id' => $location->id,
            'assigned_to_user_id' => $assignee->id,
            'status' => 'active',
        ]);

        $asset->update(['assigned_to_name' => 'New Staff Member']);

        $this->assertNull($asset->fresh()->assigned_to_user_id);
        $this->assertSame('New Staff Member', $asset->fresh()->assignee_name);
        $this->assertDatabaseHas('asset_assignment_histories', [
            'asset_id' => $asset->id,
            'assigned_to_user_id' => null,
            'assigned_to_name' => 'New Staff Member',
            'action' => 'updated',
        ]);
    }

    public function test_changing_a_legacy_user_assignee_refreshes_the_assignee_name(): void
    {
        $this->actingAs(User::factory()->create());
        $firstAssignee = User::factory()->create(['name' => 'First Assignee']);
        $secondAssignee = User::factory()->create(['name' => 'Second Assignee']);
        $asset = Asset::create([
            'asset_tag' => 'LINKED-ASSIGNEE-001',
            'serial_number' => 'LINKED-ASSIGNEE-SERIAL-001',
            'type' => 'Desktop',
            'brand' => 'Test',
            'department_id' => Department::create(['name' => 'IT'])->id,
            'location_id' => Location::create(['name' => 'Harare', 'code' => 'HAR'])->id,
            'assigned_to_user_id' => $firstAssignee->id,
            'status' => 'active',
        ]);

        $asset->update(['assigned_to_user_id' => $secondAssignee->id]);

        $this->assertSame('Second Assignee', $asset->fresh()->assignee_name);
        $this->assertDatabaseHas('asset_assignment_histories', [
            'asset_id' => $asset->id,
            'assigned_to_user_id' => $secondAssignee->id,
            'assigned_to_name' => 'Second Assignee',
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
