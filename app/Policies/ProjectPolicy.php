<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Les admins peuvent tout voir, les users seulement leurs projets.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Les admins peuvent voir tous les projets, les users seulement les leurs.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->isAdmin() || $user->id === $project->user_id;
    }

    /**
     * Tout utilisateur authentifié peut créer un projet.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Les admins peuvent modifier tous les projets, les users seulement les leurs.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->isAdmin() || $user->id === $project->user_id;
    }

    /**
     * Les admins peuvent supprimer tous les projets, les users seulement les leurs.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->isAdmin() || $user->id === $project->user_id;
    }
}
