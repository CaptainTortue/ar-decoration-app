<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user    = User::factory()->create();
        $this->project = Project::factory()->create(['user_id' => $this->user->id]);
    }

    // ── Store ─────────────────────────────────────────────────

    public function test_create_room_for_project(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/room", [
                'name'           => 'Salon',
                'width'          => 5.5,
                'length'         => 4.2,
                'height'         => 2.5,
                'floor_material' => 'Parquet',
                'floor_color'    => '#c4a882',
                'wall_material'  => 'Peinture',
                'wall_color'     => '#f5f0eb',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Salon')
            ->assertJsonPath('data.width', '5.50')
            ->assertJsonPath('data.floor.material', 'Parquet');

        $this->assertDatabaseHas('rooms', [
            'project_id' => $this->project->id,
            'name'       => 'Salon',
        ]);
    }

    public function test_create_room_replaces_existing(): void
    {
        Room::factory()->create([
            'project_id' => $this->project->id,
            'name'       => 'Ancien',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/room", [
                'name'   => 'Nouveau',
                'width'  => 4.0,
                'length' => 3.0,
                'height' => 2.5,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Nouveau');

        $this->assertDatabaseCount('rooms', 1);
        $this->assertDatabaseMissing('rooms', ['name' => 'Ancien']);
    }

    public function test_create_room_fails_without_dimensions(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$this->project->id}/room", [
                'name' => 'Salon',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['width', 'length', 'height']);
    }

    public function test_create_room_on_other_user_project_is_forbidden(): void
    {
        $otherProject = Project::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/projects/{$otherProject->id}/room", [
                'width' => 4.0, 'length' => 3.0, 'height' => 2.5,
            ]);

        $response->assertForbidden();
    }

    // ── Show ──────────────────────────────────────────────────

    public function test_show_room(): void
    {
        Room::factory()->create([
            'project_id' => $this->project->id,
            'name'       => 'Salon',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/room");

        $response->assertOk()
            ->assertJsonPath('data.name', 'Salon')
            ->assertJsonStructure(['data' => ['dimensions', 'floor', 'wall']]);
    }

    public function test_show_room_returns_404_when_no_room(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$this->project->id}/room");

        $response->assertNotFound();
    }

    // ── Update ────────────────────────────────────────────────

    public function test_update_room(): void
    {
        Room::factory()->create([
            'project_id' => $this->project->id,
            'wall_color' => '#ffffff',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$this->project->id}/room", [
                'wall_color' => '#e8e0d5',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.wall.color', '#e8e0d5');
    }

    // ── Destroy ───────────────────────────────────────────────

    public function test_delete_room(): void
    {
        Room::factory()->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/projects/{$this->project->id}/room");

        $response->assertOk();
        $this->assertDatabaseCount('rooms', 0);
    }
}
