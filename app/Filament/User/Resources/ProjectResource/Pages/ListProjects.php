<?php

namespace App\Filament\User\Resources\ProjectResource\Pages;

use App\Filament\User\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouveau projet')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $userId = auth()->id();

        return [
            'all' => Tab::make('Tous')
                ->badge(Project::where('user_id', $userId)->count())
                ->badgeColor('primary'),
            'draft' => Tab::make('Brouillons')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft'))
                ->badge(Project::where('user_id', $userId)->where('status', 'draft')->count())
                ->badgeColor('gray')
                ->icon('heroicon-o-pencil'),
            'in_progress' => Tab::make('En cours')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'in_progress'))
                ->badge(Project::where('user_id', $userId)->where('status', 'in_progress')->count())
                ->badgeColor('warning')
                ->icon('heroicon-o-arrow-path'),
            'completed' => Tab::make('Terminés')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge(Project::where('user_id', $userId)->where('status', 'completed')->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check-circle'),
        ];
    }
}
