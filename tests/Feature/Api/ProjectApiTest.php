<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user      = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    // ── Index ─────────────────────────────────────────────────

    public function test_list_projects_returns_only_own_projects(): void
    {
        Project::factory()->count(3)->create(['user_id' => $this->user->id]);
        Project::factory()->count(2)->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/projects');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_list_projects_requires_auth(): void
    {
        $this->getJson('/api/projects')->assertUnauthorized();
    }

    public function test_list_projects_includes_objects_count(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/projects');

        $response->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'objects_count']]]);
    }

    // ── Store ─────────────────────────────────────────────────

    public function test_create_project_succeeds(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/projects', [
                'name'        => 'Mon Salon',
                'description' => 'Test projet',
                'status'      => 'draft',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Mon Salon')
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonPath('data.user_id', $this->user->id);

        $this->assertDatabaseHas('projects', [
            'name'    => 'Mon Salon',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_create_project_with_scene_settings(): void
    {
        $settings = ['ambient_light' => 0.5, 'background_color' => '#ffffff'];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/projects', [
                'name'           => 'Salon',
                'scene_settings' => $settings,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.scene_settings.ambient_light', 0.5);
    }

    public function test_create_project_fails_without_name(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/projects', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_create_project_fails_with_invalid_status(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/projects', [
                'name'   => 'Test',
                'status' => 'invalid_status',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    }

    public function test_create_project_requires_auth(): void
    {
        $this->postJson('/api/projects', ['name' => 'Test'])
            ->assertUnauthorized();
    }

    // ── Show ──────────────────────────────────────────────────

    public function test_show_own_project(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$project->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $project->id)
            ->assertJsonStructure(['data' => ['id', 'name', 'room', 'objects']]);
    }

    public function test_show_other_user_project_is_forbidden(): void
    {
        $project = Project::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$project->id}");

        $response->assertForbidden();
    }

    public function test_show_nonexistent_project_returns_404(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/projects/999');

        $response->assertNotFound();
    }

    // ── Update ────────────────────────────────────────────────

    public function test_update_own_project(): void
    {
        $project = Project::factory()->create([
            'user_id' => $this->user->id,
            'name'    => 'Original',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$project->id}", [
                'name'   => 'Updated',
                'status' => 'in_progress',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated')
            ->assertJsonPath('data.status', 'in_progress');

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated']);
    }

    public function test_update_other_user_project_is_forbidden(): void
    {
        $project = Project::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$project->id}", ['name' => 'Hacked']);

        $response->assertForbidden();
    }

    // ── Destroy ───────────────────────────────────────────────

    public function test_delete_own_project(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Projet supprimé.');

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_delete_other_user_project_is_forbidden(): void
    {
        $project = Project::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertForbidden();
    }
}
