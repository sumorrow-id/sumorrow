<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\GoogleAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private GoogleAuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GoogleAuthService;
    }

    private function createGoogleUserMock(string $id, string $name, string $email, string $avatar = 'https://example.com/avatar.jpg'): object
    {
        return new class($id, $name, $email, $avatar)
        {
            public function __construct(
                public string $id,
                public string $name,
                public string $email,
                private string $avatar,
            ) {}

            public function getAvatar(): string
            {
                return $this->avatar;
            }
        };
    }

    // -------------------------------------------------------------------------
    // Create new user
    // -------------------------------------------------------------------------

    public function test_creates_new_user_when_not_exists(): void
    {
        $googleUser = $this->createGoogleUserMock('123456789', 'John Doe', 'john@example.com');

        $user = $this->service->findOrCreateUser($googleUser);

        $this->assertNotNull($user->id);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('123456789', $user->google_id);
        $this->assertEquals('John Doe', $user->username);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->password_hash);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'google_id' => '123456789',
        ]);
    }

    // -------------------------------------------------------------------------
    // Update existing user (by google_id)
    // -------------------------------------------------------------------------

    public function test_updates_user_found_by_google_id(): void
    {
        $user = User::factory()->create([
            'google_id' => '123456789',
            'email' => 'old@example.com',
            'avatar_url' => null,
        ]);

        $googleUser = $this->createGoogleUserMock('123456789', 'John Doe Updated', 'new@example.com');

        $updated = $this->service->findOrCreateUser($googleUser);

        $this->assertEquals($user->id, $updated->id);
        $this->assertEquals('https://example.com/avatar.jpg', $updated->avatar_url);
        $this->assertEquals($user->email, $updated->email);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'avatar_url' => 'https://example.com/avatar.jpg',
        ]);
    }

    // -------------------------------------------------------------------------
    // Update existing user (by email)
    // -------------------------------------------------------------------------

    public function test_updates_user_found_by_email(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'google_id' => null,
            'email_verified_at' => null,
        ]);

        $googleUser = $this->createGoogleUserMock('123456789', 'John Doe', 'john@example.com');

        $updated = $this->service->findOrCreateUser($googleUser);

        $this->assertEquals($user->id, $updated->id);
        $this->assertEquals('123456789', $updated->google_id);
        $this->assertNotNull($updated->email_verified_at);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'google_id' => '123456789',
        ]);
    }

    // -------------------------------------------------------------------------
    // Preserve email verification
    // -------------------------------------------------------------------------

    public function test_preserves_existing_email_verification(): void
    {
        $verifiedAt = now()->subDay();
        User::factory()->create([
            'google_id' => null,
            'email' => 'john@example.com',
            'email_verified_at' => $verifiedAt,
        ]);

        $googleUser = $this->createGoogleUserMock('123456789', 'John Doe', 'john@example.com');

        $updated = $this->service->findOrCreateUser($googleUser);

        $this->assertEquals($verifiedAt->format('Y-m-d'), $updated->email_verified_at->format('Y-m-d'));
    }
}
