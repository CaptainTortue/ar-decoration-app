<?php

namespace App\Filament\User\Widgets;

use App\Models\Project;
use App\Models\ProjectObject;
use App\Models\Room;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = auth()->id();

        $totalProjects = Project::where('user_id', $userId)->count();
        $completedProjects = Project::where('user_id', $userId)->where('status', 'completed')->count();
        $inProgressProjects = Project::where('user_id', $userId)->where('status', 'in_progress')->count();
        $draftProjects = Project::where('user_id', $userId)->where('status', 'draft')->count();
        $totalObjects = ProjectObject::whereHas('project', fn ($q) => $q->where('user_id', $userId))->count();
        $configuredRooms = Room::whereHas('project', fn ($q) => $q->where('user_id', $userId))->count();

        // Calcul du taux de complétion
        $completionRate = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100) : 0;

        return [
            Stat::make('Mes projets', $totalProjects)
                ->description($completedProjects.' terminés, '.$draftProjects.' brouillons')
                ->descriptionIcon('heroicon-m-folder')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: null })",
                ]),

            Stat::make('En cours', $inProgressProjects)
                ->description('Projets actifs')
                ->descriptionIcon('heroicon-m-play')
                ->color('warning')
                ->chart([3, 2, 4, 3, 4, 2, 3]),

            Stat::make('Objets placés', $totalObjects)
                ->description('Dans tous vos projets')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success')
                ->chart([2, 4, 6, 8, 7, 9, 10]),

            Stat::make('Pièces configurées', $configuredRooms)
                ->description($configuredRooms.' sur '.$totalProjects.' projets')
                ->descriptionIcon('heroicon-m-home')
                ->color('info'),
        ];
    }
}
