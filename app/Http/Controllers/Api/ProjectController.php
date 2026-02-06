<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    /**
     * Liste les projets.
     * Admin : tous les projets
     * User : seulement ses projets
     */
    public function index(Request $request)
    {
        $query = $request->user()->isAdmin()
            ? Project::query()
            : $request->user()->projects();

        $projects = $query
            ->withCount('projectObjects')
            ->with('user:id,name')
            ->orderBy('updated_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return ProjectResource::collection($projects);
    }

    /**
     * Crée un nouveau projet.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = $request->user()->projects()->create(
            $request->validated()
        );

        return new ProjectResource($project->load('room', 'projectObjects'));
    }

    /**
     * Affiche un projet avec sa pièce et ses objets.
     */
    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        $project->load([
            'room',
            'projectObjects.furnitureObject.category',
        ]);

        return new ProjectResource($project);
    }

    /**
     * Met à jour un projet.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        Gate::authorize('update', $project);

        $project->update($request->validated());

        return new ProjectResource($project->load('room', 'projectObjects'));
    }

    /**
     * Supprime un projet.
     */
    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return response()->json(['message' => 'Projet supprimé.'], 200);
    }
}
