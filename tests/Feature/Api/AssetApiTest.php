<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\FurnitureObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetApiTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private FurnitureObject $activeObject;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->category = Category::factory()->create(['name' => 'Tables', 'slug' => 'tables']);

        // Créer les fichiers de test
        Storage::disk('public')->put('models/furniture/test-table.glb', 'fake glb content');
        Storage::disk('public')->put('thumbnails/furniture/test-table.webp', 'fake image content');

        $this->activeObject = FurnitureObject::factory()->create([
            'name' => 'Test Table',
            'slug' => 'test-table',
            'category_id' => $this->category->id,
            'model_path' => 'models/furniture/test-table.glb',
            'thumbnail_path' => 'thumbnails/furniture/test-table.webp',
            'is_active' => true,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    //  Model Download
    // ══════════════════════════════════════════════════════════

    public function test_download_model_returns_glb_file(): void
    {
        $response = $this->get("/api/furniture-objects/{$this->activeObject->id}/model");

        $response->assertOk()
            ->assertHeader('Content-Type', 'model/gltf-binary')
            ->assertHeader('Access-Control-Allow-Origin', '*');
    }

    public function test_download_model_returns_404_for_inactive_object(): void
    {
        $inactiveObject = FurnitureObject::factory()->create([
            'category_id' => $this->category->id,
            'model_path' => 'models/furniture/inactive.glb',
            'is_active' => false,
        ]);

        $response = $this->get("/api/furniture-objects/{$inactiveObject->id}/model");

        $response->assertNotFound();
    }

    public function test_download_model_returns_404_for_empty_path(): void
    {
        $objectWithoutModel = FurnitureObject::factory()->create([
            'category_id' => $this->category->id,
            'model_path' => '', // Chemin vide au lieu de null
            'is_active' => true,
        ]);

        $response = $this->get("/api/furniture-objects/{$objectWithoutModel->id}/model");

        $response->assertNotFound();
    }

    public function test_download_model_returns_404_for_missing_file(): void
    {
        $objectWithMissingFile = FurnitureObject::factory()->create([
            'category_id' => $this->category->id,
            'model_path' => 'models/furniture/nonexistent.glb',
            'is_active' => true,
        ]);

        $response = $this->get("/api/furniture-objects/{$objectWithMissingFile->id}/model");

        $response->assertNotFound();
    }

    public function test_download_model_returns_404_for_nonexistent_object(): void
    {
        $response = $this->get('/api/furniture-objects/99999/model');

        $response->assertNotFound();
    }

    // ══════════════════════════════════════════════════════════
    //  Thumbnail Download
    // ══════════════════════════════════════════════════════════

    public function test_download_thumbnail_returns_image(): void
    {
        $response = $this->get("/api/furniture-objects/{$this->activeObject->id}/thumbnail");

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/webp')
            ->assertHeader('Access-Control-Allow-Origin', '*');
    }

    public function test_download_thumbnail_returns_correct_content_type_for_png(): void
    {
        Storage::disk('public')->put('thumbnails/furniture/test-png.png', 'fake png content');

        $objectWithPng = FurnitureObject::factory()->create([
            'category_id' => $this->category->id,
            'thumbnail_path' => 'thumbnails/furniture/test-png.png',
            'model_path' => 'models/furniture/test-table.glb',
            'is_active' => true,
        ]);

        $response = $this->get("/api/furniture-objects/{$objectWithPng->id}/thumbnail");

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_download_thumbnail_returns_correct_content_type_for_jpeg(): void
    {
        Storage::disk('public')->put('thumbnails/furniture/test-jpg.jpg', 'fake jpg content');

        $objectWithJpg = FurnitureObject::factory()->create([
            'category_id' => $this->category->id,
            'thumbnail_path' => 'thumbnails/furniture/test-jpg.jpg',
            'model_path' => 'models/furniture/test-table.glb',
            'is_active' => true,
        ]);

        $response = $this->get("/api/furniture-objects/{$objectWithJpg->id}/thumbnail");

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_download_thumbnail_returns_404_for_inactive_object(): void
    {
        $inactiveObject = FurnitureObject::factory()->create([
            'category_id' => $this->category->id,
            'thumbnail_path' => 'thumbnails/furniture/inactive.webp',
            'is_active' => false,
        ]);

        $response = $this->get("/api/furniture-objects/{$inactiveObject->id}/thumbnail");

        $response->assertNotFound();
    }

    public function test_download_thumbnail_returns_404_for_missing_path(): void
    {
        $objectWithoutThumbnail = FurnitureObject::factory()->create([
            'category_id' => $this->category->id,
            'thumbnail_path' => null,
            'model_path' => 'models/furniture/test-table.glb',
            'is_active' => true,
        ]);

        $response = $this->get("/api/furniture-objects/{$objectWithoutThumbnail->id}/thumbnail");

        $response->assertNotFound();
    }

    public function test_download_thumbnail_returns_404_for_missing_file(): void
    {
        $objectWithMissingFile = FurnitureObject::factory()->create([
            'category_id' => $this->category->id,
            'thumbnail_path' => 'thumbnails/furniture/nonexistent.webp',
            'model_path' => 'models/furniture/test-table.glb',
            'is_active' => true,
        ]);

        $response = $this->get("/api/furniture-objects/{$objectWithMissingFile->id}/thumbnail");

        $response->assertNotFound();
    }

    // ══════════════════════════════════════════════════════════
    //  Model Streaming
    // ══════════════════════════════════════════════════════════

    public function test_stream_model_returns_glb_file(): void
    {
        $response = $this->get("/api/furniture-objects/{$this->activeObject->id}/model/stream");

        $response->assertOk()
            ->assertHeader('Content-Type', 'model/gltf-binary')
            ->assertHeader('Accept-Ranges', 'bytes')
            ->assertHeader('Access-Control-Allow-Origin', '*');
    }

    public function test_stream_model_supports_range_requests(): void
    {
        // Créer un fichier plus grand pour tester les range requests
        $content = str_repeat('x', 1000);
        Storage::disk('public')->put('models/furniture/large-model.glb', $content);

        $largeObject = FurnitureObject::factory()->create([
            'category_id' => $this->category->id,
            'model_path' => 'models/furniture/large-model.glb',
            'is_active' => true,
        ]);

        $response = $this->get(
            "/api/furniture-objects/{$largeObject->id}/model/stream",
            ['Range' => 'bytes=0-499']
        );

        $response->assertStatus(206)
            ->assertHeader('Content-Range');
    }

    public function test_stream_model_returns_404_for_inactive_object(): void
    {
        $inactiveObject = FurnitureObject::factory()->create([
            'category_id' => $this->category->id,
            'model_path' => 'models/furniture/inactive.glb',
            'is_active' => false,
        ]);

        $response = $this->get("/api/furniture-objects/{$inactiveObject->id}/model/stream");

        $response->assertNotFound();
    }

    // ══════════════════════════════════════════════════════════
    //  CORS Headers
    // ══════════════════════════════════════════════════════════

    public function test_model_endpoint_has_cors_headers(): void
    {
        $response = $this->get("/api/furniture-objects/{$this->activeObject->id}/model");

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', '*')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_thumbnail_endpoint_has_cors_headers(): void
    {
        $response = $this->get("/api/furniture-objects/{$this->activeObject->id}/thumbnail");

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', '*')
            ->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_options_request_returns_cors_headers(): void
    {
        $response = $this->options("/api/furniture-objects/{$this->activeObject->id}/model");

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin')
            ->assertHeader('Access-Control-Allow-Methods')
            ->assertHeader('Access-Control-Allow-Headers');
    }

    // ══════════════════════════════════════════════════════════
    //  Cache Headers
    // ══════════════════════════════════════════════════════════

    public function test_model_has_cache_headers(): void
    {
        $response = $this->get("/api/furniture-objects/{$this->activeObject->id}/model");

        $response->assertOk()
            ->assertHeader('Cache-Control');
    }

    public function test_thumbnail_has_cache_headers(): void
    {
        $response = $this->get("/api/furniture-objects/{$this->activeObject->id}/thumbnail");

        $response->assertOk()
            ->assertHeader('Cache-Control');
    }

    // ══════════════════════════════════════════════════════════
    //  Routes are public (no auth required)
    // ══════════════════════════════════════════════════════════

    public function test_asset_routes_are_public(): void
    {
        // Sans authentification
        $this->get("/api/furniture-objects/{$this->activeObject->id}/model")->assertOk();
        $this->get("/api/furniture-objects/{$this->activeObject->id}/thumbnail")->assertOk();
        $this->get("/api/furniture-objects/{$this->activeObject->id}/model/stream")->assertOk();
    }
}
