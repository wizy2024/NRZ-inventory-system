<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Asset;
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

    public function test_seeded_machine_is_not_assigned_to_a_technician(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertNull(Asset::where('asset_tag', 'NRZ-IT-0002')->value('assigned_to_user_id'));
    }
}
