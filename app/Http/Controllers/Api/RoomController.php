<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
    /**
     * Affiche la pièce d'un projet.
     */
    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        $room = $project->room;

        if (!$room) {
            return response()->json(['message' => 'Aucune pièce définie pour ce projet.'], 404);
        }

        return new RoomResource($room);
    }

    /**
     * Crée ou remplace la pièce d'un projet.
     */
    public function store(StoreRoomRequest $request, Project $project)
    {
        Gate::authorize('update', $project);

        // Supprimer l'ancienne pièce si elle existe
        $project->room()->delete();

        $room = $project->room()->create($request->validated());

        return new RoomResource($room);
    }

    /**
     * Met à jour la pièce d'un projet.
     */
    public function update(UpdateRoomRequest $request, Project $project)
    {
        Gate::authorize('update', $project);

        $room = $project->room;

        if (!$room) {
            return response()->json(['message' => 'Aucune pièce définie pour ce projet.'], 404);
        }

        $room->update($request->validated());

        return new RoomResource($room);
    }

    /**
     * Supprime la pièce d'un projet.
     */
    public function destroy(Project $project)
    {
        Gate::authorize('update', $project);

        $project->room()->delete();

        return response()->json(['message' => 'Pièce supprimée.'], 200);
    }
}
