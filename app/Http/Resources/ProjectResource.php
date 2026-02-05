<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'thumbnail_url'  => $this->thumbnail_path ? Storage::url($this->thumbnail_path) : null,
            'status'         => $this->status,
            'scene_settings' => $this->scene_settings,
            'user_id'        => $this->user_id,
            'room'           => new RoomResource($this->whenLoaded('room')),
            'objects'        => ProjectObjectResource::collection($this->whenLoaded('projectObjects')),
            'objects_count'  => $this->whenCounted('projectObjects'),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
