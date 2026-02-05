<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\FurnitureObject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FurnitureObjectFactory extends Factory
{
    protected $model = FurnitureObject::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name'                => ucfirst($name),
            'slug'                => Str::slug($name),
            'description'         => fake()->sentence(),
            'category_id'         => Category::factory(),
            'model_path'          => 'models/furniture/' . Str::slug($name) . '.glb',
            'thumbnail_path'      => 'thumbnails/furniture/' . Str::slug($name) . '.webp',
            'width'               => fake()->randomFloat(3, 0.1, 3.0),
            'height'              => fake()->randomFloat(3, 0.1, 3.0),
            'depth'               => fake()->randomFloat(3, 0.1, 3.0),
            'default_scale'       => 1.000,
            'available_colors'    => ['Blanc', 'Noir'],
            'available_materials' => ['Bois', 'Métal'],
            'price'               => fake()->randomFloat(2, 10, 1000),
            'is_active'           => true,
        ];
    }
}
