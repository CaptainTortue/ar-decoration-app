<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Category;
use App\Models\FurnitureObject;
use App\Models\Project;
use App\Models\ProjectObject;
use App\Models\Room;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $adminUsers = User::where('is_admin', true)->count();
        $regularUsers = $totalUsers - $adminUsers;

        $totalProjects = Project::count();
        $completedProjects = Project::where('status', 'completed')->count();
        $inProgressProjects = Project::where('status', 'in_progress')->count();
        $draftProjects = Project::where('status', 'draft')->count();

        $totalObjects = FurnitureObject::count();
        $activeObjects = FurnitureObject::where('is_active', true)->count();

        $placedObjects = ProjectObject::count();
        $configuredRooms = Room::count();

        return [
            Stat::make('Utilisateurs', $totalUsers)
                ->description($adminUsers.' admin(s), '.$regularUsers.' utilisateur(s)')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),

            Stat::make('Projets', $totalProjects)
                ->description($completedProjects.' terminés, '.$inProgressProjects.' en cours, '.$draftProjects.' brouillons')
                ->descriptionIcon('heroicon-m-folder')
                ->color('success')
                ->chart([2, 4, 6, 8, 7, 9, 10, 12]),

            Stat::make('Objets 3D', $totalObjects)
                ->description($activeObjects.' actifs dans le catalogue')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning')
                ->chart([5, 6, 7, 8, 9, 10, 11, 12]),

            Stat::make('Catégories', Category::count())
                ->description(Category::whereNull('parent_id')->count().' catégories racines')
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),

            Stat::make('Objets placés', $placedObjects)
                ->description('Dans '.$totalProjects.' projet(s)')
                ->descriptionIcon('heroicon-m-squares-plus')
                ->color('danger'),

            Stat::make('Pièces configurées', $configuredRooms)
                ->description(round($totalProjects > 0 ? ($configuredRooms / $totalProjects) * 100 : 0).'% des projets')
                ->descriptionIcon('heroicon-m-home')
                ->color('gray'),
        ];
    }
}
