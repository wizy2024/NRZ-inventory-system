<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Asset;
use App\Models\Audit;
use App\Models\GatePass;
use App\Models\Location;
use App\Models\MaintenanceLog;
use App\Models\User;
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

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $permissions = [
            'view assets',
            'create assets',
            'edit assets',
            'delete assets',
            'condemn assets',
            'view maintenance',
            'manage maintenance',
            'manage users',
            'view reports',
            'manage audits',
            'manage gate passes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'Administrator' => $permissions,
            'Inventory Manager' => [
                'view assets', 'create assets', 'edit assets', 'delete assets',
                'condemn assets', 'view maintenance', 'manage maintenance', 'view reports',
                'manage audits', 'manage gate passes',
            ],
            'Technician' => ['view assets', 'view maintenance', 'manage maintenance'],
            'Auditor' => ['view assets', 'view maintenance', 'view reports', 'manage audits'],
            'Read Only' => ['view assets', 'view maintenance'],
        ];

        foreach ($roles as $name => $rolePermissions) {
            Role::findOrCreate($name, 'web')->syncPermissions($rolePermissions);
        }

        foreach ([
            'IT',
            'Finance',
            'HR',
            'Audit',
            'Security',
            'Traffic',
            'Marketing',
        ] as $name) {
            Department::updateOrCreate(['name' => $name]);
        }

        foreach ([
            'Bulawayo',
            'Rutenga',
            'Harare',
            'Lowveld',
        ] as $name) {
            Location::updateOrCreate(
                ['name' => $name],
                ['code' => strtoupper(substr($name, 0, 3))],
            );
        }

        $testUser = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $testUser->assignRole('Administrator');

        $demoUsers = [
            [
                'name' => 'Thandiwe Moyo',
                'email' => 'thandiwe.moyo@example.com',
                'role' => 'Inventory Manager',
            ],
            [
                'name' => 'Brian Ncube',
                'email' => 'brian.ncube@example.com',
                'role' => 'Technician',
            ],
            [
                'name' => 'Rudo Chikwanha',
                'email' => 'rudo.chikwanha@example.com',
                'role' => 'Auditor',
            ],
        ];

        $users = collect($demoUsers)->mapWithKeys(function (array $data): array {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );

            $user->assignRole($data['role']);

            return [$data['role'] => $user];
        });

        $departments = Department::whereIn('name', [
            'IT', 'Finance', 'HR', 'Security',
        ])->get()->keyBy('name');

        $locations = Location::whereIn('name', [
            'Harare', 'Bulawayo', 'Rutenga',
        ])->get()->keyBy('name');

        $assetData = [
            [
                'asset_tag' => 'NRZ-LAP-0001',
                'serial_number' => 'HP840G8-DEMO-001',
                'mac_address' => '00:25:96:FF:10:01',
                'type' => 'Laptop',
                'brand' => 'HP',
                'specs' => ['model' => 'EliteBook 840 G8', 'ram' => '16GB', 'storage' => '512GB SSD'],
                'purchase_date' => '2025-01-15',
                'warranty_expiry' => '2027-01-15',
                'department_id' => $departments['IT']->id,
                'location_id' => $locations['Harare']->id,
                'assigned_to_user_id' => $users['Inventory Manager']->id,
                'assigned_at' => now()->subMonths(5),
                'assignment_notes' => 'Issued for inventory administration.',
                'status' => 'active',
            ],
            [
                'asset_tag' => 'NRZ-DESK-0002',
                'serial_number' => 'DELLOPTIPLEX-DEMO-002',
                'mac_address' => '00:25:96:FF:10:02',
                'type' => 'Desktop',
                'brand' => 'Dell',
                'specs' => ['model' => 'OptiPlex 7090', 'ram' => '16GB', 'storage' => '1TB SSD'],
                'purchase_date' => '2024-08-10',
                'warranty_expiry' => '2026-08-10',
                'department_id' => $departments['Finance']->id,
                'location_id' => $locations['Bulawayo']->id,
                'assigned_to_user_id' => null,
                'assigned_at' => null,
                'assignment_notes' => null,
                'status' => 'maintenance',
            ],
            [
                'asset_tag' => 'NRZ-PRN-0003',
                'serial_number' => 'HP4103FDN-DEMO-003',
                'mac_address' => null,
                'type' => 'Printer',
                'brand' => 'HP',
                'specs' => ['model' => 'LaserJet Pro M404dn', 'connection' => 'Network'],
                'purchase_date' => '2023-05-22',
                'warranty_expiry' => '2026-05-22',
                'department_id' => $departments['HR']->id,
                'location_id' => $locations['Harare']->id,
                'assigned_to_user_id' => null,
                'assigned_at' => null,
                'assignment_notes' => null,
                'status' => 'active',
            ],
            [
                'asset_tag' => 'NRZ-SRV-0004',
                'serial_number' => 'DELLR740-DEMO-004',
                'mac_address' => '00:25:96:FF:10:04',
                'type' => 'Server',
                'brand' => 'Dell',
                'specs' => ['model' => 'PowerEdge R740', 'ram' => '64GB', 'storage' => '4TB RAID'],
                'purchase_date' => '2022-11-03',
                'warranty_expiry' => '2027-11-03',
                'department_id' => $departments['IT']->id,
                'location_id' => $locations['Harare']->id,
                'assigned_to_user_id' => $users['Technician']->id,
                'assigned_at' => now()->subYear(),
                'assignment_notes' => 'Primary inventory application server.',
                'status' => 'active',
            ],
            [
                'asset_tag' => 'NRZ-RAD-0005',
                'serial_number' => 'MOTOROLA-RADIO-DEMO-005',
                'mac_address' => null,
                'type' => 'Radio',
                'brand' => 'Motorola',
                'specs' => ['model' => 'DP4400e', 'frequency' => 'UHF'],
                'purchase_date' => '2021-03-14',
                'warranty_expiry' => '2024-03-14',
                'department_id' => $departments['Security']->id,
                'location_id' => $locations['Rutenga']->id,
                'assigned_to_user_id' => null,
                'assigned_at' => null,
                'assignment_notes' => null,
                'status' => 'decommissioned',
                'condemnation_reason' => 'Battery no longer holds a safe charge.',
                'condemned_at' => now()->subMonths(2)->toDateString(),
            ],
        ];

        $assets = collect($assetData)->mapWithKeys(function (array $data): array {
            $asset = Asset::updateOrCreate(
                ['asset_tag' => $data['asset_tag']],
                $data,
            );

            return [$asset->asset_tag => $asset];
        });

        $maintenanceLog = MaintenanceLog::updateOrCreate(
            ['asset_id' => $assets['NRZ-DESK-0002']->id, 'symptom' => 'Paper feed errors'],
            [
                'technician_id' => $users['Technician']->id,
                'description' => 'The printer intermittently jams after multiple pages are printed.',
                'status' => 'in_progress',
                'resolved_at' => null,
                'resolution_notes' => null,
            ],
        );

        MaintenanceLog::updateOrCreate(
            ['asset_id' => $assets['NRZ-LAP-0001']->id, 'symptom' => 'Battery health warning'],
            [
                'technician_id' => $users['Technician']->id,
                'description' => 'Battery capacity has fallen below the recommended threshold.',
                'status' => 'resolved',
                'resolved_at' => now()->subDays(12),
                'resolution_notes' => 'Battery replaced and device tested successfully.',
            ],
        );

        Audit::updateOrCreate(
            ['asset_id' => $assets['NRZ-LAP-0001']->id, 'checked_at' => now()->subDays(4)->startOfDay()],
            [
                'audited_by' => $users['Auditor']->id,
                'result' => 'found',
                'notes' => 'Asset present, assigned user confirmed, and condition acceptable.',
            ],
        );

        Audit::updateOrCreate(
            ['asset_id' => $assets['NRZ-RAD-0005']->id, 'checked_at' => now()->subDays(8)->startOfDay()],
            [
                'audited_by' => $users['Auditor']->id,
                'result' => 'missing',
                'notes' => 'Decommissioned radio is awaiting disposal documentation.',
            ],
        );

            GatePass::updateOrCreate(
            ['pass_number' => 'GP-DEMO-0001'],
            [
                'asset_id' => $assets['NRZ-DESK-0002']->id,
                'maintenance_log_id' => $maintenanceLog->id,
                'collector_name' => 'Mandla Sibanda',
                'collector_contact' => '+263 77 000 0001',
                'collector_id_number' => '63-123456-A-12',
                'issued_by' => $users['Inventory Manager']->id,
                'released_at' => now()->subDays(2),
                'notes' => 'Sent to approved service centre for printer repair.',
            ],
            );
        });
    }
}
