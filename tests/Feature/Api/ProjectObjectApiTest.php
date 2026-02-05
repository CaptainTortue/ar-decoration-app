<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\FurnitureObject;
use App\Models\Project;
use App\Models\ProjectObject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectObjectApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;
    private FurnitureObject $furniture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user     = User::factory()->create();
        $this->project  = Project::factory()->create(['user_id' => $this->user->id]);
        $category       = Category::factory()->create();
        $this->furniture = FurnitureObject::factory()->create(['category_id' => $category->id]);
    }

    // ── Index ─────────────────────────────────────────────────

    public function test_list_objects_in_project(): void
    {
        ProjectObject::factory()->count(3)->create([
            'project_id'          => $this->project->id,
            'furniture_object_id' => $this->furniture->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/objects");

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_list_objects_includes_furniture_details(): void
    {
        ProjectObject::factory()->create([
            'project_id'          => $this->project->id,
            'furniture_object_id' => $this->furniture->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/objects");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'furniture_object' => ['id', 'name', 'category']]],
            ]);
    }

    public function test_list_objects_on_other_user_project_is_forbidden(): void
    {
        $otherProject = Project::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$otherProject->id}/objects");

        $response->assertForbidden();
    }

    // ── Store ─────────────────────────────────────────────────

    public function test_add_object_to_project(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/objects", [
                'furniture_object_id' => $this->furniture->id,
                'position_x'         => 2.5,
                'position_y'         => 0,
                'position_z'         => 1.8,
                'rotation_y'         => 45,
                'color'              => 'Blanc',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.position.x', '2.5000')
            ->assertJsonPath('data.rotation.y', '45.0000')
            ->assertJsonPath('data.color', 'Blanc');

        $this->assertDatabaseHas('project_objects', [
            'project_id'          => $this->project->id,
            'furniture_object_id' => $this->furniture->id,
        ]);
    }

    public function test_add_object_fails_with_invalid_furniture_id(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/objects", [
                'furniture_object_id' => 999,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('furniture_object_id');
    }

    public function test_add_object_fails_without_furniture_id(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/objects", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('furniture_object_id');
    }

    // ── Show ──────────────────────────────────────────────────

    public function test_show_object_in_project(): void
    {
        $object = ProjectObject::factory()->create([
            'project_id'          => $this->project->id,
            'furniture_object_id' => $this->furniture->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/objects/{$object->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $object->id)
            ->assertJsonStructure(['data' => ['position', 'rotation', 'scale', 'furniture_object']]);
    }

    public function test_show_object_from_other_project_returns_404(): void
    {
        $otherProject = Project::factory()->create(['user_id' => $this->user->id]);
        $object = ProjectObject::factory()->create([
            'project_id'          => $otherProject->id,
            'furniture_object_id' => $this->furniture->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/objects/{$object->id}");

        $response->assertNotFound();
    }

    // ── Update ────────────────────────────────────────────────

    public function test_update_object_position(): void
    {
        $object = ProjectObject::factory()->create([
            'project_id'          => $this->project->id,
            'furniture_object_id' => $this->furniture->id,
            'position_x'          => 0,
            'position_z'          => 0,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$this->project->id}/objects/{$object->id}", [
                'position_x' => 3.5,
                'position_z' => 2.0,
                'rotation_y' => 90,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.position.x', '3.5000')
            ->assertJsonPath('data.rotation.y', '90.0000');
    }

    public function test_update_object_lock_and_visibility(): void
    {
        $object = ProjectObject::factory()->create([
            'project_id'          => $this->project->id,
            'furniture_object_id' => $this->furniture->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$this->project->id}/objects/{$object->id}", [
                'is_locked'  => true,
                'is_visible' => false,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.is_locked', true)
            ->assertJsonPath('data.is_visible', false);
    }

    // ── Destroy ───────────────────────────────────────────────

    public function test_delete_object_from_project(): void
    {
        $object = ProjectObject::factory()->create([
            'project_id'          => $this->project->id,
            'furniture_object_id' => $this->furniture->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/projects/{$this->project->id}/objects/{$object->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Objet supprimé du projet.');

        $this->assertDatabaseMissing('project_objects', ['id' => $object->id]);
    }

    public function test_delete_object_on_other_user_project_is_forbidden(): void
    {
        $otherProject = Project::factory()->create();
        $object = ProjectObject::factory()->create([
            'project_id'          => $otherProject->id,
            'furniture_object_id' => $this->furniture->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/projects/{$otherProject->id}/objects/{$object->id}");

        $response->assertForbidden();
    }
}
