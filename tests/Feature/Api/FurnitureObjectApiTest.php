<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\FurnitureObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FurnitureObjectApiTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create(['name' => 'Tables', 'slug' => 'tables']);
    }

    // ── Index ─────────────────────────────────────────────────

    public function test_list_objects_returns_only_active(): void
    {
        FurnitureObject::factory()->create(['name' => 'Active', 'is_active' => true, 'category_id' => $this->category->id]);
        FurnitureObject::factory()->create(['name' => 'Inactive', 'is_active' => false, 'category_id' => $this->category->id]);

        $response = $this->getJson('/api/furniture-objects');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Active');
    }

    public function test_list_objects_includes_category(): void
    {
        FurnitureObject::factory()->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/furniture-objects');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'category' => ['id', 'name']]],
            ]);
    }

    // ── Filtrage par category_id ──────────────────────────────

    public function test_filter_by_category_id(): void
    {
        $other = Category::factory()->create(['name' => 'Lampes']);
        FurnitureObject::factory()->create(['name' => 'Table', 'category_id' => $this->category->id]);
        FurnitureObject::factory()->create(['name' => 'Lampe', 'category_id' => $other->id]);

        $response = $this->getJson("/api/furniture-objects?category_id={$this->category->id}");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Table');
    }

    // ── Filtrage par slug de catégorie ────────────────────────

    public function test_filter_by_category_slug(): void
    {
        $other = Category::factory()->create(['name' => 'Lampes', 'slug' => 'lampes']);
        FurnitureObject::factory()->create(['name' => 'Table', 'category_id' => $this->category->id]);
        FurnitureObject::factory()->create(['name' => 'Lampe', 'category_id' => $other->id]);

        $response = $this->getJson('/api/furniture-objects?category=tables');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Table');
    }

    // ── Recherche par nom ────────────────────────────────────

    public function test_search_by_name(): void
    {
        FurnitureObject::factory()->create(['name' => 'Table à manger', 'category_id' => $this->category->id]);
        FurnitureObject::factory()->create(['name' => 'Table basse', 'category_id' => $this->category->id]);
        FurnitureObject::factory()->create(['name' => 'Chaise', 'category_id' => $this->category->id]);

        $response = $this->getJson('/api/furniture-objects?search=Table');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    // ── Pagination ───────────────────────────────────────────

    public function test_pagination(): void
    {
        FurnitureObject::factory()->count(10)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/furniture-objects?per_page=3&page=1');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.total', 10)
            ->assertJsonPath('meta.per_page', 3);
    }

    // ── Show ──────────────────────────────────────────────────

    public function test_show_object_returns_details(): void
    {
        $object = FurnitureObject::factory()->create([
            'name'        => 'Table à manger',
            'category_id' => $this->category->id,
            'price'       => 349.99,
        ]);

        $response = $this->getJson("/api/furniture-objects/{$object->id}");

        $response->assertOk()
            ->assertJsonPath('data.name', 'Table à manger')
            ->assertJsonPath('data.price', '349.99')
            ->assertJsonStructure([
                'data' => ['id', 'name', 'slug', 'model_url', 'thumbnail_url', 'dimensions', 'category'],
            ]);
    }

    public function test_show_object_returns_404_for_nonexistent(): void
    {
        $response = $this->getJson('/api/furniture-objects/999');

        $response->assertNotFound();
    }

    // ── Route publique ───────────────────────────────────────

    public function test_routes_are_public(): void
    {
        $object = FurnitureObject::factory()->create(['category_id' => $this->category->id]);

        $this->getJson('/api/furniture-objects')->assertOk();
        $this->getJson("/api/furniture-objects/{$object->id}")->assertOk();
    }
}
