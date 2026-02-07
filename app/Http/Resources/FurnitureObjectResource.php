<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FurnitureObjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'slug'                => $this->slug,
            'description'         => $this->description,
            'category_id'         => $this->category_id,
            'category'            => new CategoryResource($this->whenLoaded('category')),

            // URLs pour accès direct via le stockage public (legacy)
            'model_url'           => $this->model_path ? Storage::url($this->model_path) : null,
            'thumbnail_url'       => $this->thumbnail_path ? Storage::url($this->thumbnail_path) : null,

            // URLs API sécurisées pour applications externes (VR/AR/front-end)
            'assets' => [
                'model'           => route('api.furniture-objects.model', $this->id),
                'model_stream'    => route('api.furniture-objects.model.stream', $this->id),
                'thumbnail'       => route('api.furniture-objects.thumbnail', $this->id),
            ],

            'dimensions' => [
                'width'  => $this->width,
                'height' => $this->height,
                'depth'  => $this->depth,
            ],
            'default_scale'       => $this->default_scale,
            'available_colors'    => $this->available_colors,
            'available_materials' => $this->available_materials,
            'price'               => $this->price,
            'is_active'           => $this->is_active,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
        ];
    }
}
