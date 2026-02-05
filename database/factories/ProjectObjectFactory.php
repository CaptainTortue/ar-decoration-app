<?php

namespace Database\Factories;

use App\Models\FurnitureObject;
use App\Models\Project;
use App\Models\ProjectObject;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectObjectFactory extends Factory
{
    protected $model = ProjectObject::class;

    public function definition(): array
    {
        return [
            'project_id'          => Project::factory(),
            'furniture_object_id' => FurnitureObject::factory(),
            'position_x'          => fake()->randomFloat(4, -10, 10),
            'position_y'          => 0,
            'position_z'          => fake()->randomFloat(4, -10, 10),
            'rotation_x'          => 0,
            'rotation_y'          => fake()->randomFloat(4, 0, 360),
            'rotation_z'          => 0,
            'scale_x'             => 1.000,
            'scale_y'             => 1.000,
            'scale_z'             => 1.000,
            'color'               => null,
            'material'            => null,
            'is_locked'           => false,
            'is_visible'          => true,
        ];
    }
}
