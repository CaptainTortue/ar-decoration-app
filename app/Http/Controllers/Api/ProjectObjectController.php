<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectObjectRequest;
use App\Http\Requests\UpdateProjectObjectRequest;
use App\Http\Resources\ProjectObjectResource;
use App\Models\Project;
use App\Models\ProjectObject;
use Illuminate\Support\Facades\Gate;

class ProjectObjectController extends Controller
{
    /**
     * Liste les objets placés dans un projet.
     */
    public function index(Project $project)
    {
        Gate::authorize('view', $project);

        $objects = $project->projectObjects()
            ->with('furnitureObject.category')
            ->get();

        return ProjectObjectResource::collection($objects);
    }

    /**
     * Ajoute un objet dans un projet.
     */
    public function store(StoreProjectObjectRequest $request, Project $project)
    {
        Gate::authorize('update', $project);

        $projectObject = $project->projectObjects()->create(
            $request->validated()
        );

        $projectObject->load('furnitureObject.category');

        return new ProjectObjectResource($projectObject);
    }

    /**
     * Affiche un objet placé dans un projet.
     */
    public function show(Project $project, ProjectObject $object)
    {
        Gate::authorize('view', $project);

        $this->ensureObjectBelongsToProject($project, $object);

        $object->load('furnitureObject.category');

        return new ProjectObjectResource($object);
    }

    /**
     * Met à jour un objet placé (position, rotation, échelle, etc.).
     */
    public function update(UpdateProjectObjectRequest $request, Project $project, ProjectObject $object)
    {
        Gate::authorize('update', $project);

        $this->ensureObjectBelongsToProject($project, $object);

        $object->update($request->validated());

        $object->load('furnitureObject.category');

        return new ProjectObjectResource($object);
    }

    /**
     * Supprime un objet du projet.
     */
    public function destroy(Project $project, ProjectObject $object)
    {
        Gate::authorize('update', $project);

        $this->ensureObjectBelongsToProject($project, $object);

        $object->delete();

        return response()->json(['message' => 'Objet supprimé du projet.'], 200);
    }

    /**
     * Vérifie que l'objet appartient bien au projet.
     */
    private function ensureObjectBelongsToProject(Project $project, ProjectObject $object): void
    {
        if ($object->project_id !== $project->id) {
            abort(404, 'Objet introuvable dans ce projet.');
        }
    }
}
