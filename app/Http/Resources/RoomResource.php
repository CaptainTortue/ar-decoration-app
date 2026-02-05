<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'project_id'        => $this->project_id,
            'name'              => $this->name,
            'dimensions' => [
                'width'  => $this->width,
                'length' => $this->length,
                'height' => $this->height,
            ],
            'floor' => [
                'material' => $this->floor_material,
                'color'    => $this->floor_color,
            ],
            'wall' => [
                'material' => $this->wall_material,
                'color'    => $this->wall_color,
            ],
            'lighting_settings' => $this->lighting_settings,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
