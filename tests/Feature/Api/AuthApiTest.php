<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    // ── Login ────────────────────────────────────────────────

    public function test_login_returns_token_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email'       => 'test@example.com',
            'password'    => 'password',
            'device_name' => 'phpunit',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user'])
            ->assertJsonPath('user.email', 'test@example.com');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $response = $this->postJson('/api/login', [
            'email'       => 'test@example.com',
            'password'    => 'wrong-password',
            'device_name' => 'phpunit',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_login_fails_with_missing_fields(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password', 'device_name']);
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email'       => 'nobody@example.com',
            'password'    => 'password',
            'device_name' => 'phpunit',
        ]);

        $response->assertUnprocessable();
    }

    // ── Get User ─────────────────────────────────────────────

    public function test_get_user_returns_authenticated_user(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/user');

        $response->assertOk()
            ->assertJsonPath('email', 'test@example.com');
    }

    public function test_get_user_fails_without_token(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertUnauthorized();
    }

    // ── Logout ───────────────────────────────────────────────

    public function test_logout_revokes_token(): void
    {
        // Créer un vrai token
        $accessToken = $this->user->createToken('phpunit');
        $token = $accessToken->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/logout');

        $response->assertOk()
            ->assertJsonPath('message', 'Déconnexion réussie.');

        // Vérifier que le token a été supprimé en base
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $accessToken->accessToken->id,
        ]);
    }

    public function test_logout_fails_without_token(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertUnauthorized();
    }
}
