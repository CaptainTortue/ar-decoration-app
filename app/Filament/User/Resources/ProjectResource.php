<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\ProjectResource\Pages;
use App\Filament\User\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-folder';
    }

    public static function getNavigationLabel(): string
    {
        return 'Mes projets';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getModelLabel(): string
    {
        return 'Projet';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mes projets';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations du projet')
                    ->description('Définissez les informations de base de votre projet')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom du projet')
                            ->placeholder('Ex: Salon moderne, Chambre d\'enfant...')
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->columnSpan(1),
                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'draft' => 'Brouillon',
                                'in_progress' => 'En cours',
                                'completed' => 'Terminé',
                            ])
                            ->required()
                            ->default('draft')
                            ->native(false)
                            ->columnSpan(1),
                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Décrivez votre projet de décoration...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Paramètres de scène 3D')
                    ->description('Configuration avancée de la scène de réalité augmentée')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        KeyValue::make('scene_settings')
                            ->label('Paramètres')
                            ->keyLabel('Paramètre')
                            ->valueLabel('Valeur')
                            ->addActionLabel('Ajouter un paramètre')
                            ->keyPlaceholder('Ex: lighting_intensity')
                            ->valuePlaceholder('Ex: 0.8')
                            ->reorderable(),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom du projet')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Project $record): string => $record->description ? \Str::limit($record->description, 50) : 'Aucune description'),
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
                    ->sortable()
                    ->badge()
                    ->color('info'),
                IconColumn::make('has_room')
                    ->label('Pièce')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->room !== null)
                    ->trueIcon('heroicon-o-home')
                    ->falseIcon('heroicon-o-home-modern')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Dernière modification')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'in_progress' => 'En cours',
                        'completed' => 'Terminé',
                    ])
                    ->native(false),
                Filter::make('has_room')
                    ->label('Avec pièce configurée')
                    ->query(fn (Builder $query): Builder => $query->whereHas('room')),
                Filter::make('has_objects')
                    ->label('Avec objets')
                    ->query(fn (Builder $query): Builder => $query->whereHas('projectObjects')),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Voir'),
                    EditAction::make()
                        ->label('Modifier'),
                    Action::make('duplicate')
                        ->label('Dupliquer')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Dupliquer le projet')
                        ->modalDescription('Une copie du projet sera créée avec tous ses objets et paramètres de pièce.')
                        ->modalSubmitActionLabel('Dupliquer')
                        ->action(function (Project $record) {
                            $newProject = $record->replicate();
                            $newProject->name = $record->name.' (copie)';
                            $newProject->status = 'draft';
                            $newProject->save();

                            if ($record->room) {
                                $newRoom = $record->room->replicate();
                                $newRoom->project_id = $newProject->id;
                                $newRoom->save();
                            }

                            foreach ($record->projectObjects as $object) {
                                $newObject = $object->replicate();
                                $newObject->project_id = $newProject->id;
                                $newObject->save();
                            }

                            Notification::make()
                                ->title('Projet dupliqué')
                                ->body("Le projet \"{$newProject->name}\" a été créé avec succès.")
                                ->success()
                                ->send();
                        }),
                    Action::make('change_status')
                        ->label('Changer le statut')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Select::make('status')
                                ->label('Nouveau statut')
                                ->options([
                                    'draft' => 'Brouillon',
                                    'in_progress' => 'En cours',
                                    'completed' => 'Terminé',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (Project $record, array $data) {
                            $oldStatus = $record->status;
                            $record->update(['status' => $data['status']]);

                            $statusLabels = [
                                'draft' => 'Brouillon',
                                'in_progress' => 'En cours',
                                'completed' => 'Terminé',
                            ];

                            Notification::make()
                                ->title('Statut modifié')
                                ->body("Le statut est passé de \"{$statusLabels[$oldStatus]}\" à \"{$statusLabels[$data['status']]}\".")
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make()
                        ->label('Supprimer')
                        ->modalHeading('Supprimer le projet')
                        ->modalDescription(fn (Project $record) => "Êtes-vous sûr de vouloir supprimer le projet \"{$record->name}\" ? Cette action est irréversible et supprimera également :\n- ".$record->projectObjects->count()." objet(s) placé(s)\n- La configuration de la pièce (si existante)")
                        ->modalSubmitActionLabel('Oui, supprimer définitivement'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Actions'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('change_status_bulk')
                        ->label('Changer le statut')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Select::make('status')
                                ->label('Nouveau statut')
                                ->options([
                                    'draft' => 'Brouillon',
                                    'in_progress' => 'En cours',
                                    'completed' => 'Terminé',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(fn ($record) => $record->update(['status' => $data['status']]));

                            Notification::make()
                                ->title('Statuts modifiés')
                                ->body(count($records).' projet(s) mis à jour.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make()
                        ->label('Supprimer la sélection')
                        ->modalHeading('Supprimer les projets sélectionnés')
                        ->modalDescription('Êtes-vous sûr de vouloir supprimer les projets sélectionnés ? Cette action est irréversible et supprimera également tous les objets et configurations de pièce associés.')
                        ->modalSubmitActionLabel('Oui, supprimer tout'),
                ]),
            ])
            ->emptyStateHeading('Aucun projet')
            ->emptyStateDescription('Créez votre premier projet de décoration pour commencer.')
            ->emptyStateIcon('heroicon-o-folder-plus')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Créer un projet')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProjectObjectsRelationManager::class,
            RelationManagers\RoomRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
