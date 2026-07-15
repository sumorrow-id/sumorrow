<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_promotes_existing_user_to_admin(): void
    {
        $user = User::factory()->create(['email' => 'boss@example.com']);

        config(['auth.admin_emails' => 'boss@example.com']);
        $this->seed(AdminSeeder::class);

        $this->assertSame('admin', $user->fresh()->role);
    }

    public function test_creates_missing_user_as_admin_and_is_idempotent(): void
    {
        config(['auth.admin_emails' => 'new@example.com']);
        $this->seed(AdminSeeder::class);

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertSame('admin', $user->role);
        $this->assertSame('new', $user->username);
        $this->assertNull($user->password_hash);

        $this->seed(AdminSeeder::class);
        $this->assertSame(1, User::where('email', 'new@example.com')->count());
    }

    public function test_falls_back_to_full_email_when_username_is_taken(): void
    {
        User::factory()->create(['username' => 'new', 'email' => 'other@example.com']);

        config(['auth.admin_emails' => 'new@example.com']);
        $this->seed(AdminSeeder::class);

        $this->assertSame('new@example.com', User::where('email', 'new@example.com')->value('username'));
    }

    public function test_does_nothing_when_admin_emails_is_empty(): void
    {
        $userCount = User::count();

        config(['auth.admin_emails' => '']);
        $this->seed(AdminSeeder::class);

        $this->assertSame($userCount, User::count());
        $this->assertSame(0, User::where('role', 'admin')->count());
    }
}
