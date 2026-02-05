<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectObjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'project_id'          => $this->project_id,
            'furniture_object_id' => $this->furniture_object_id,
            'furniture_object'    => new FurnitureObjectResource($this->whenLoaded('furnitureObject')),
            'position' => [
                'x' => $this->position_x,
                'y' => $this->position_y,
                'z' => $this->position_z,
            ],
            'rotation' => [
                'x' => $this->rotation_x,
                'y' => $this->rotation_y,
                'z' => $this->rotation_z,
            ],
            'scale' => [
                'x' => $this->scale_x,
                'y' => $this->scale_y,
                'z' => $this->scale_z,
            ],
            'color'      => $this->color,
            'material'   => $this->material,
            'is_locked'  => $this->is_locked,
            'is_visible' => $this->is_visible,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
