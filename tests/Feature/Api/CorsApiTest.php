<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\FurnitureObject;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Tests CORS pour toutes les routes API.
 * Vérifie que les headers CORS sont présents sur toutes les réponses.
 */
class CorsApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    // ══════════════════════════════════════════════════════════
    //  Routes publiques - CORS Headers
    // ══════════════════════════════════════════════════════════

    public function test_categories_index_has_cors_headers(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_categories_show_has_cors_headers(): void
    {
        $response = $this->getJson("/api/categories/{$this->category->id}");

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_furniture_objects_index_has_cors_headers(): void
    {
        FurnitureObject::factory()->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/furniture-objects');

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_furniture_objects_show_has_cors_headers(): void
    {
        $object = FurnitureObject::factory()->create(['category_id' => $this->category->id]);

        $response = $this->getJson("/api/furniture-objects/{$object->id}");

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    // ══════════════════════════════════════════════════════════
    //  Routes protégées - CORS Headers
    // ══════════════════════════════════════════════════════════

    public function test_projects_index_has_cors_headers(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/projects');

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_projects_create_has_cors_headers(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/projects', [
            'name' => 'Test Project',
        ]);

        $response->assertCreated()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_projects_show_has_cors_headers(): void
    {
        Sanctum::actingAs($this->user);
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/projects/{$project->id}");

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_projects_update_has_cors_headers(): void
    {
        Sanctum::actingAs($this->user);
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/projects/{$project->id}", [
            'name' => 'Updated Project',
        ]);

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_projects_delete_has_cors_headers(): void
    {
        Sanctum::actingAs($this->user);
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertSuccessful()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    // ══════════════════════════════════════════════════════════
    //  Auth routes - CORS Headers
    // ══════════════════════════════════════════════════════════

    public function test_login_has_cors_headers(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'test',
        ]);

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_user_endpoint_has_cors_headers(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/user');

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    // ══════════════════════════════════════════════════════════
    //  Room routes - CORS Headers
    // ══════════════════════════════════════════════════════════

    public function test_room_create_has_cors_headers(): void
    {
        Sanctum::actingAs($this->user);
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson("/api/projects/{$project->id}/room", [
            'width' => 5.0,
            'length' => 4.0,
            'height' => 2.5,
        ]);

        $response->assertCreated()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    // ══════════════════════════════════════════════════════════
    //  Project Objects routes - CORS Headers
    // ══════════════════════════════════════════════════════════

    public function test_project_objects_index_has_cors_headers(): void
    {
        Sanctum::actingAs($this->user);
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/projects/{$project->id}/objects");

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    // ══════════════════════════════════════════════════════════
    //  Error responses - CORS Headers
    // ══════════════════════════════════════════════════════════

    public function test_404_response_has_cors_headers(): void
    {
        $response = $this->getJson('/api/categories/99999');

        $response->assertNotFound()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_401_response_has_cors_headers(): void
    {
        $response = $this->getJson('/api/projects');

        $response->assertUnauthorized()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_403_response_has_cors_headers(): void
    {
        Sanctum::actingAs($this->user);
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/projects/{$project->id}");

        $response->assertForbidden()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_422_validation_error_has_cors_headers(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/projects', []);

        $response->assertUnprocessable()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    // ══════════════════════════════════════════════════════════
    //  OPTIONS preflight requests
    // ══════════════════════════════════════════════════════════

    public function test_options_login_returns_cors_headers(): void
    {
        $response = $this->options('/api/login', [], [
            'Origin' => 'http://localhost:3000',
            'Access-Control-Request-Method' => 'POST',
        ]);

        // OPTIONS peut retourner 200 ou 204
        $response->assertSuccessful()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods')
            ->assertHeader('Access-Control-Allow-Headers');
    }

    public function test_options_categories_returns_cors_headers(): void
    {
        $response = $this->options('/api/categories', [], [
            'Origin' => 'http://localhost:3000',
            'Access-Control-Request-Method' => 'GET',
        ]);

        $response->assertSuccessful()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods')
            ->assertHeader('Access-Control-Allow-Headers');
    }

    public function test_options_projects_returns_cors_headers(): void
    {
        $response = $this->options('/api/projects', [], [
            'Origin' => 'http://localhost:3000',
            'Access-Control-Request-Method' => 'POST',
            'Access-Control-Request-Headers' => 'Authorization, Content-Type',
        ]);

        $response->assertSuccessful()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods')
            ->assertHeader('Access-Control-Allow-Headers');
    }
}
