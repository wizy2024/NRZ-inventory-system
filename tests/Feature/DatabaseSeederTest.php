<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Department;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_and_legacy_administrator_accounts_are_seeded(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (['admin@example.com', 'test@example.com'] as $email) {
            $user = User::where('email', $email)->first();

            $this->assertNotNull($user, $email);
            $this->assertTrue($user->hasRole('Administrator'), $email);
            $this->assertTrue(Hash::check('password', $user->password), $email);
        }
    }

    public function test_department_assignee_pool_excludes_technicians_and_other_departments(): void
    {
        $this->seed(DatabaseSeeder::class);

        $itDepartment = Department::where('name', 'IT')->firstOrFail();
        $eligibleIds = User::eligibleAssetAssignees($itDepartment->id)->pluck('id')->all();

        $this->assertContains(User::where('email', 'thandiwe.moyo@example.com')->value('id'), $eligibleIds);
        $this->assertNotContains(User::where('email', 'brian.ncube@example.com')->value('id'), $eligibleIds);
        $this->assertNotContains(User::where('email', 'rudo.chikwanha@example.com')->value('id'), $eligibleIds);
        $this->assertDatabaseHas('assets', [
            'asset_tag' => 'NRZ-IT-0002',
            'assigned_to_user_id' => null,
        ]);
    }
}
