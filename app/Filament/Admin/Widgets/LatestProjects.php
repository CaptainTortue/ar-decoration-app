<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestProjects extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()->latest()->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nom du projet')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Project $record): string => $record->description ? \Str::limit($record->description, 40) : ''),
                TextColumn::make('user.name')
                    ->label('Propriétaire')
                    ->description(fn (Project $record): string => $record->user->email)
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'in_progress' => 'heroicon-o-arrow-path',
                        'completed' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Brouillon',
                        'in_progress' => 'En cours',
                        'completed' => 'Terminé',
                        default => $state,
                    }),
                TextColumn::make('project_objects_count')
                    ->label('Objets')
                    ->counts('projectObjects')
                    ->badge()
                    ->color('info'),
                IconColumn::make('has_room')
                    ->label('Pièce')
                    ->boolean()
                    ->getStateUsing(fn (Project $record) => $record->room !== null)
                    ->trueIcon('heroicon-o-home')
                    ->falseIcon('heroicon-o-home-modern')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->since()
                    ->sortable(),
            ])
            ->heading('Derniers projets')
            ->headerActions([
                Action::make('view_all')
                    ->label('Voir tous les projets')
                    ->url(ProjectResource::getUrl('index'))
                    ->icon('heroicon-o-arrow-right')
                    ->color('gray'),
            ])
            ->actions([
                Action::make('view')
                    ->label('Voir')
                    ->url(fn (Project $record): string => ProjectResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-m-eye'),
                Action::make('edit')
                    ->label('Modifier')
                    ->url(fn (Project $record): string => ProjectResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-pencil')
                    ->color('gray'),
            ])
            ->emptyStateHeading('Aucun projet')
            ->emptyStateDescription('Les projets créés par les utilisateurs apparaîtront ici.')
            ->emptyStateIcon('heroicon-o-folder')
            ->paginated(false);
    }
}
