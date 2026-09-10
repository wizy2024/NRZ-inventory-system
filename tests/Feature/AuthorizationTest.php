<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Audit;
use App\Models\Department;
use App\Models\GatePass;
use App\Models\Location;
use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_read_only_user_cannot_delete_any_managed_record_or_bulk_delete(): void
    {
        $readOnly = Role::create([
            'name' => 'Read Only',
            'guard_name' => 'web',
        ]);
        $readOnly->syncPermissions([
            Permission::create(['name' => 'view assets', 'guard_name' => 'web']),
            Permission::create(['name' => 'view maintenance', 'guard_name' => 'web']),
        ]);
        $user = User::factory()->create();
        $user->assignRole($readOnly);

        foreach ([Asset::class, Audit::class, GatePass::class, MaintenanceLog::class, User::class] as $modelClass) {
            $record = new $modelClass;

            $this->assertFalse(Gate::forUser($user)->allows('delete', $record), $modelClass);
            $this->assertFalse(Gate::forUser($user)->allows('deleteAny', $modelClass), $modelClass . ' bulk');
        }
    }

    public function test_permissioned_user_can_delete_assets_and_bulk_delete_assets(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::create([
            'name' => 'delete assets',
            'guard_name' => 'web',
        ]));

        $this->assertTrue(Gate::forUser($user)->allows('delete', new Asset));
        $this->assertTrue(Gate::forUser($user)->allows('deleteAny', Asset::class));
    }

    public function test_asset_qr_endpoint_requires_a_valid_signature(): void
    {
        $asset = Asset::create([
            'asset_tag' => 'TEST-001',
            'serial_number' => 'SERIAL-001',
            'type' => 'Laptop',
            'brand' => 'Test',
            'department_id' => Department::create(['name' => 'IT'])->id,
            'location_id' => Location::create(['name' => 'Harare', 'code' => 'HAR'])->id,
            'status' => 'active',
        ]);

        $this->get(route('assets.info', $asset))
            ->assertForbidden();

        $this->get(URL::signedRoute('assets.info', ['asset' => $asset]))
            ->assertOk();
    }

    public function test_asset_qr_url_uses_the_configured_shareable_base_url(): void
    {
        config()->set('app.qr_base_url', 'http://192.168.1.56:8000');

        $asset = Asset::create([
            'asset_tag' => 'QR-001',
            'serial_number' => 'QR-SERIAL-001',
            'type' => 'Laptop',
            'brand' => 'Test',
            'department_id' => Department::create(['name' => 'IT'])->id,
            'location_id' => Location::create(['name' => 'Harare', 'code' => 'HAR'])->id,
            'status' => 'active',
        ]);

        config()->set('app.url', config('app.qr_base_url'));

        $url = URL::signedRoute('assets.info', ['asset' => $asset]);

        $this->assertStringStartsWith("http://192.168.1.56:8000/device/{$asset->id}/info?", $url);
    }
}
