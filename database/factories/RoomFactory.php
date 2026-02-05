<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'project_id'        => Project::factory(),
            'name'              => fake()->word(),
            'width'             => fake()->randomFloat(2, 2.0, 10.0),
            'length'            => fake()->randomFloat(2, 2.0, 10.0),
            'height'            => fake()->randomFloat(2, 2.0, 4.0),
            'floor_material'    => 'Parquet',
            'floor_color'       => '#c4a882',
            'wall_material'     => 'Peinture',
            'wall_color'        => '#f5f0eb',
            'lighting_settings' => null,
        ];
    }
}
