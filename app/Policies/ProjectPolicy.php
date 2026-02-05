<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * L'utilisateur ne peut voir que ses propres projets.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    /**
     * Tout utilisateur authentifié peut créer un projet.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * L'utilisateur ne peut modifier que ses propres projets.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    /**
     * L'utilisateur ne peut supprimer que ses propres projets.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }
}
