<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\FurnitureObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    // ── Index ─────────────────────────────────────────────────

    public function test_list_categories_returns_only_root_categories(): void
    {
        $parent = Category::factory()->create(['name' => 'Meubles', 'parent_id' => null]);
        $child  = Category::factory()->create(['name' => 'Tables', 'parent_id' => $parent->id]);

        $response = $this->getJson('/api/categories');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Meubles')
            ->assertJsonPath('data.0.children.0.name', 'Tables');
    }

    public function test_list_categories_is_ordered_by_sort_order(): void
    {
        Category::factory()->create(['name' => 'Décoration', 'sort_order' => 3]);
        Category::factory()->create(['name' => 'Meubles', 'sort_order' => 1]);
        Category::factory()->create(['name' => 'Luminaires', 'sort_order' => 2]);

        $response = $this->getJson('/api/categories');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Meubles')
            ->assertJsonPath('data.1.name', 'Luminaires')
            ->assertJsonPath('data.2.name', 'Décoration');
    }

    public function test_list_categories_returns_empty_when_none_exist(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_list_categories_is_public(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertOk();
    }

    // ── Show ──────────────────────────────────────────────────

    public function test_show_category_returns_details_with_relations(): void
    {
        $category = Category::factory()->create(['name' => 'Meubles']);
        $child    = Category::factory()->create(['name' => 'Tables', 'parent_id' => $category->id]);
        FurnitureObject::factory()->create(['category_id' => $category->id]);

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertOk()
            ->assertJsonPath('data.name', 'Meubles')
            ->assertJsonStructure([
                'data' => ['id', 'name', 'slug', 'description', 'children', 'furniture_objects_count'],
            ]);
    }

    public function test_show_category_returns_404_for_nonexistent(): void
    {
        $response = $this->getJson('/api/categories/999');

        $response->assertNotFound();
    }
}
