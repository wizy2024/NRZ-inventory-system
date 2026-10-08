<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetAssignmentHistory;
use App\Models\Audit;
use App\Models\Department;
use App\Models\GatePass;
use App\Models\InventoryReport;
use App\Models\Location;
use App\Models\MaintenanceAssignmentHistory;
use App\Models\MaintenanceLog;
use App\Models\User;
use App\Notifications\MaintenanceAssigned;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->seedPermissionsAndRoles();
            $departments = $this->seedDepartments();
            $locations = $this->seedLocations();
            $users = $this->seedUsers();
            $assets = $this->seedAssets($departments, $locations, $users);
            $maintenance = $this->seedMaintenance($assets, $users);

            $this->seedAssignmentHistory($assets, $users);
            $this->seedMaintenanceAssignmentHistory($maintenance, $users);
            $this->seedAudits($assets, $users);
            $this->seedGatePass($assets['NRZ-HR-0001'], $maintenance['resolved'], $users['Inventory Manager']);
            $this->seedReports($users['Inventory Manager']);
            $this->seedTechnicianNotification($maintenance['active'], $users['Technician 2']);
        });
    }

    private function seedPermissionsAndRoles(): void
    {
        $permissions = [
            'view assets', 'create assets', 'edit assets', 'delete assets',
            'condemn assets', 'view maintenance', 'manage maintenance',
            'manage users', 'view reports', 'manage audits', 'manage gate passes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'Administrator' => $permissions,
            'Inventory Manager' => [
                'view assets', 'create assets', 'edit assets', 'delete assets',
                'condemn assets', 'view maintenance', 'manage maintenance',
                'view reports', 'manage audits', 'manage gate passes',
            ],
            'Technician' => ['view assets', 'view maintenance', 'manage maintenance'],
            'Auditor' => ['view assets', 'view maintenance', 'view reports', 'manage audits'],
            'Read Only' => ['view assets', 'view maintenance'],
        ];

        foreach ($roles as $name => $rolePermissions) {
            Role::findOrCreate($name, 'web')->syncPermissions($rolePermissions);
        }
    }

    private function seedDepartments(): array
    {
        $departments = [];

        foreach (['IT', 'Finance', 'HR', 'Audit', 'Security', 'Traffic', 'Marketing'] as $name) {
            $departments[$name] = Department::updateOrCreate(['name' => $name]);
        }

        return $departments;
    }

    private function seedLocations(): array
    {
        $locations = [];

        foreach (['Harare', 'Bulawayo', 'Rutenga', 'Lowveld'] as $name) {
            $locations[$name] = Location::updateOrCreate(
                ['name' => $name],
                ['code' => strtoupper(substr($name, 0, 3))],
            );
        }

        return $locations;
    }

    private function seedUsers(): array
    {
        $definitions = [
            'Administrator' => ['name' => 'Test Administrator', 'email' => 'admin@example.com', 'role' => 'Administrator'],
            'Legacy Administrator' => ['name' => 'Test User', 'email' => 'test@example.com', 'role' => 'Administrator'],
            'Inventory Manager' => ['name' => 'Thandiwe Moyo', 'email' => 'thandiwe.moyo@example.com', 'role' => 'Inventory Manager'],
            'Technician' => ['name' => 'Brian Ncube', 'email' => 'brian.ncube@example.com', 'role' => 'Technician'],
            'Technician 2' => ['name' => 'Nomsa Dube', 'email' => 'nomsa.dube@example.com', 'role' => 'Technician'],
            'Auditor' => ['name' => 'Rudo Chikwanha', 'email' => 'rudo.chikwanha@example.com', 'role' => 'Auditor'],
            'Read Only' => ['name' => 'Peter Ndlovu', 'email' => 'peter.ndlovu@example.com', 'role' => 'Read Only'],
        ];

        $users = [];

        foreach ($definitions as $key => $definition) {
            $user = User::updateOrCreate(
                ['email' => $definition['email']],
                [
                    'name' => $definition['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );

            $user->syncRoles([$definition['role']]);
            $users[$key] = $user;
        }

        return $users;
    }

    private function seedAssets(array $departments, array $locations, array $users): array
    {
        $definitions = [
            ['tag' => 'NRZ-IT-0001', 'serial' => 'HP840G8-DEMO-001', 'mac' => '00:25:96:FF:10:01', 'type' => 'Laptop', 'brand' => 'HP', 'department' => 'IT', 'location' => 'Harare', 'user' => 'Inventory Manager', 'status' => 'active', 'warranty' => '2027-01-15'],
            ['tag' => 'NRZ-IT-0002', 'serial' => 'DELLR740-DEMO-002', 'mac' => '00:25:96:FF:10:02', 'type' => 'Server', 'brand' => 'Dell', 'department' => 'IT', 'location' => 'Harare', 'user' => null, 'status' => 'active', 'warranty' => '2027-11-03'],
            ['tag' => 'NRZ-FIN-0001', 'serial' => 'DELOPTI-DEMO-003', 'mac' => '00:25:96:FF:10:03', 'type' => 'Desktop', 'brand' => 'Dell', 'department' => 'Finance', 'location' => 'Bulawayo', 'user' => null, 'status' => 'active', 'warranty' => '2026-08-10'],
            ['tag' => 'NRZ-HR-0001', 'serial' => 'HP4103-DEMO-004', 'mac' => null, 'type' => 'Printer', 'brand' => 'HP', 'department' => 'HR', 'location' => 'Harare', 'user' => null, 'status' => 'active', 'warranty' => '2026-12-20'],
            ['tag' => 'NRZ-AUD-0001', 'serial' => 'LENOVO-T14-DEMO-005', 'mac' => '00:25:96:FF:10:05', 'type' => 'Laptop', 'brand' => 'Lenovo', 'department' => 'Audit', 'location' => 'Harare', 'user' => 'Auditor', 'status' => 'active', 'warranty' => '2027-04-11'],
            ['tag' => 'NRZ-SEC-0001', 'serial' => 'MOTO-DP4400-DEMO-006', 'mac' => null, 'type' => 'Other', 'brand' => 'Motorola', 'department' => 'Security', 'location' => 'Rutenga', 'user' => null, 'status' => 'active', 'warranty' => '2026-10-30'],
            ['tag' => 'NRZ-TRF-0001', 'serial' => 'PANASONIC-TOUGH-DEMO-007', 'mac' => '00:25:96:FF:10:07', 'type' => 'Laptop', 'brand' => 'Panasonic', 'department' => 'Traffic', 'location' => 'Lowveld', 'user' => null, 'status' => 'active', 'warranty' => '2028-02-18'],
            ['tag' => 'NRZ-MKT-0001', 'serial' => 'EPSON-PROJECTOR-DEMO-008', 'mac' => null, 'type' => 'Other', 'brand' => 'Epson', 'department' => 'Marketing', 'location' => 'Bulawayo', 'user' => null, 'status' => 'active', 'warranty' => null],
            ['tag' => 'NRZ-SEC-0002', 'serial' => 'MOTO-OLD-DEMO-009', 'mac' => null, 'type' => 'Other', 'brand' => 'Motorola', 'department' => 'Security', 'location' => 'Rutenga', 'user' => null, 'status' => 'decommissioned', 'warranty' => '2024-03-14', 'condemnation_reason' => 'Battery no longer holds a safe charge.', 'condemned_at' => now()->subMonths(2)->toDateString()],
        ];

        $assets = [];

        foreach ($definitions as $definition) {
            $asset = Asset::updateOrCreate(
                ['asset_tag' => $definition['tag']],
                [
                    'serial_number' => $definition['serial'],
                    'mac_address' => $definition['mac'],
                    'type' => $definition['type'],
                    'brand' => $definition['brand'],
                    'specs' => ['model' => $definition['brand'] . ' demonstration device', 'condition' => $definition['status'] === 'active' ? 'Operational' : 'Condemned'],
                    'purchase_date' => '2024-01-15',
                    'warranty_expiry' => $definition['warranty'],
                    'department_id' => $departments[$definition['department']]->id,
                    'location_id' => $locations[$definition['location']]->id,
                    'assigned_to_user_id' => $definition['user'] ? $users[$definition['user']]->id : null,
                    'assigned_at' => $definition['user'] ? now()->subMonths(5) : null,
                    'assignment_notes' => $definition['user'] ? 'Demonstration assignment for ' . $definition['department'] . '.' : null,
                    'status' => $definition['status'],
                    'condemnation_reason' => $definition['condemnation_reason'] ?? null,
                    'condemned_at' => $definition['condemned_at'] ?? null,
                ],
            );

            $assets[$definition['tag']] = $asset;
        }

        return $assets;
    }

    private function seedMaintenance(array $assets, array $users): array
    {
        $active = MaintenanceLog::updateOrCreate(
            ['asset_id' => $assets['NRZ-FIN-0001']->id, 'symptom' => 'Unexpected shutdowns'],
            [
                'technician_id' => $users['Technician 2']->id,
                'description' => 'Finance desktop shuts down after extended use and requires inspection.',
                'status' => 'in_progress',
                'resolved_at' => null,
                'resolution_notes' => null,
            ],
        );

        $resolved = MaintenanceLog::updateOrCreate(
            ['asset_id' => $assets['NRZ-HR-0001']->id, 'symptom' => 'Paper feed errors'],
            [
                'technician_id' => $users['Technician 2']->id,
                'description' => 'Printer intermittently jams after multiple pages are printed.',
                'status' => 'resolved',
                'resolved_at' => now()->subDays(12),
                'resolution_notes' => 'Feed rollers cleaned and test pages printed successfully.',
            ],
        );

        $pending = MaintenanceLog::updateOrCreate(
            ['asset_id' => $assets['NRZ-SEC-0001']->id, 'symptom' => 'Radio battery warning'],
            [
                'technician_id' => $users['Technician 2']->id,
                'description' => 'Battery health warning reported during security equipment check.',
                'status' => 'pending',
                'resolved_at' => null,
                'resolution_notes' => null,
            ],
        );

        return ['active' => $active, 'resolved' => $resolved, 'pending' => $pending];
    }

    private function seedAssignmentHistory(array $assets, array $users): void
    {
        foreach ($assets as $asset) {
            AssetAssignmentHistory::updateOrCreate(
                ['asset_id' => $asset->id, 'effective_at' => now()->subMonths(5)->startOfDay()],
                [
                    'assigned_to_user_id' => $asset->assigned_to_user_id,
                    'department_id' => $asset->department_id,
                    'location_id' => $asset->location_id,
                    'changed_by' => $users['Inventory Manager']->id,
                    'assigned_at' => $asset->assigned_at,
                    'action' => 'created',
                    'assignment_notes' => $asset->assignment_notes,
                ],
            );
        }
    }

    private function seedMaintenanceAssignmentHistory(array $maintenance, array $users): void
    {
        MaintenanceAssignmentHistory::updateOrCreate(
            ['maintenance_log_id' => $maintenance['active']->id, 'action' => 'created'],
            ['technician_id' => $users['Technician']->id, 'changed_by' => $users['Inventory Manager']->id, 'effective_at' => now()->subDays(3)],
        );

        MaintenanceAssignmentHistory::updateOrCreate(
            ['maintenance_log_id' => $maintenance['active']->id, 'action' => 'transferred'],
            ['technician_id' => $users['Technician 2']->id, 'changed_by' => $users['Inventory Manager']->id, 'effective_at' => now()->subDay()],
        );

        MaintenanceAssignmentHistory::updateOrCreate(
            ['maintenance_log_id' => $maintenance['resolved']->id, 'action' => 'created'],
            ['technician_id' => $users['Technician 2']->id, 'changed_by' => $users['Inventory Manager']->id, 'effective_at' => now()->subDays(14)],
        );
    }

    private function seedAudits(array $assets, array $users): void
    {
        Audit::updateOrCreate(
            ['asset_id' => $assets['NRZ-IT-0001']->id, 'checked_at' => now()->subDays(4)->startOfDay()],
            [
                'audited_by' => $users['Auditor']->id,
                'result' => 'found',
                'expected_location' => 'Harare',
                'observed_location' => 'Harare',
                'expected_assignee' => 'Thandiwe Moyo',
                'notes' => 'Asset present, assigned user confirmed, and condition acceptable.',
                'follow_up_status' => 'not_required',
            ],
        );

        Audit::updateOrCreate(
            ['asset_id' => $assets['NRZ-TRF-0001']->id, 'checked_at' => now()->subDays(2)->startOfDay()],
            [
                'audited_by' => $users['Auditor']->id,
                'result' => 'wrong_location',
                'expected_location' => 'Lowveld',
                'observed_location' => 'Rutenga',
                'expected_assignee' => null,
                'notes' => 'Asset found at another station; transfer confirmation is required.',
                'follow_up_status' => 'open',
                'follow_up_owner_id' => $users['Inventory Manager']->id,
                'follow_up_due_at' => now()->addDays(5),
                'follow_up_notes' => 'Confirm the approved transfer and update the asset location.',
            ],
        );

        Audit::updateOrCreate(
            ['asset_id' => $assets['NRZ-SEC-0002']->id, 'checked_at' => now()->subDays(8)->startOfDay()],
            [
                'audited_by' => $users['Auditor']->id,
                'result' => 'missing',
                'expected_location' => 'Rutenga',
                'observed_location' => 'Not found',
                'expected_assignee' => null,
                'notes' => 'Decommissioned radio is awaiting disposal documentation.',
                'follow_up_status' => 'in_progress',
                'follow_up_owner_id' => $users['Inventory Manager']->id,
                'follow_up_due_at' => now()->addDays(2),
                'follow_up_notes' => 'Locate the disposal record and attach the approved evidence.',
            ],
        );
    }

    private function seedGatePass(Asset $asset, MaintenanceLog $maintenance, User $issuer): void
    {
        GatePass::updateOrCreate(
            ['pass_number' => 'GP-DEMO-0001'],
            [
                'asset_id' => $asset->id,
                'maintenance_log_id' => $maintenance->id,
                'collector_name' => 'Mandla Sibanda',
                'collector_contact' => '+263 77 000 0001',
                'collector_id_number' => '63-123456-A-12',
                'issued_by' => $issuer->id,
                'released_at' => now()->subDays(2),
                'notes' => 'Collected after printer repair and functional testing.',
            ],
        );
    }

    private function seedReports(User $generator): void
    {
        InventoryReport::updateOrCreate(
            ['title' => 'Demonstration inventory summary'],
            [
                'report_type' => 'inventory_summary',
                'summary' => [
                    'Total assets' => Asset::count(),
                    'Active assets' => Asset::where('status', 'active')->count(),
                    'Decommissioned' => Asset::where('status', 'decommissioned')->count(),
                    'Departments represented' => Asset::distinct('department_id')->count('department_id'),
                ],
                'generated_by' => $generator->id,
                'generated_at' => now()->subDay(),
                'notes' => 'Repeatable demonstration snapshot covering every department.',
            ],
        );

        InventoryReport::updateOrCreate(
            ['title' => 'Demonstration maintenance report'],
            [
                'report_type' => 'maintenance',
                'summary' => [
                    'Total maintenance logs' => MaintenanceLog::count(),
                    'Pending' => MaintenanceLog::where('status', 'pending')->count(),
                    'In progress' => MaintenanceLog::where('status', 'in_progress')->count(),
                    'Resolved' => MaintenanceLog::where('status', 'resolved')->count(),
                    'Assets affected' => MaintenanceLog::distinct('asset_id')->count('asset_id'),
                ],
                'generated_by' => $generator->id,
                'generated_at' => now()->subHours(12),
                'notes' => 'Demonstrates active work, pending work, and completed work.',
            ],
        );
    }

    private function seedTechnicianNotification(MaintenanceLog $maintenance, User $technician): void
    {
        DB::table('notifications')->where('type', MaintenanceAssigned::class)->delete();

        $technician->notify(new MaintenanceAssigned($maintenance));
    }
}
