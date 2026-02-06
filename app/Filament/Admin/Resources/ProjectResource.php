<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProjectResource\Pages;
use App\Filament\Admin\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
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

    public static function getModelLabel(): string
    {
        return 'Projet';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Projets';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
                    ->description('Gérez les informations de base du projet')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Select::make('user_id')
                            ->label('Propriétaire')
                            ->relationship('user', 'name')
                            ->getOptionLabelFromRecordUsing(fn (User $record) => "{$record->name} ({$record->email})")
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('name')
                            ->label('Nom du projet')
                            ->required()
                            ->maxLength(255)
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
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Paramètres de scène 3D')
                    ->description('Configuration avancée de la scène')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        KeyValue::make('scene_settings')
                            ->label('Paramètres')
                            ->keyLabel('Paramètre')
                            ->valueLabel('Valeur')
                            ->addActionLabel('Ajouter un paramètre')
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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Project $record): string => $record->description ? \Str::limit($record->description, 40) : ''),
                TextColumn::make('user.name')
                    ->label('Propriétaire')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Project $record): string => $record->user->email),
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
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Propriétaire')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
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
                    Action::make('transfer')
                        ->label('Transférer')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->color('info')
                        ->form([
                            Select::make('new_user_id')
                                ->label('Nouveau propriétaire')
                                ->options(User::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Project $record, array $data) {
                            $oldUser = $record->user->name;
                            $newUser = User::find($data['new_user_id'])->name;
                            $record->update(['user_id' => $data['new_user_id']]);

                            Notification::make()
                                ->title('Projet transféré')
                                ->body("Le projet a été transféré de {$oldUser} à {$newUser}.")
                                ->success()
                                ->send();
                        }),
                    Action::make('duplicate')
                        ->label('Dupliquer')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->form([
                            Select::make('target_user_id')
                                ->label('Propriétaire de la copie')
                                ->options(User::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->default(fn (Project $record) => $record->user_id),
                        ])
                        ->action(function (Project $record, array $data) {
                            $newProject = $record->replicate();
                            $newProject->name = $record->name.' (copie)';
                            $newProject->user_id = $data['target_user_id'];
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
                                ->body("Le projet \"{$newProject->name}\" a été créé.")
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make()
                        ->label('Supprimer')
                        ->modalHeading('Supprimer le projet')
                        ->modalDescription(fn (Project $record) => "Êtes-vous sûr de vouloir supprimer le projet \"{$record->name}\" appartenant à {$record->user->name} ? Cette action supprimera également :\n- ".$record->projectObjects->count()." objet(s)\n- La configuration de la pièce")
                        ->modalSubmitActionLabel('Oui, supprimer'),
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
                    BulkAction::make('transfer_bulk')
                        ->label('Transférer')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->form([
                            Select::make('new_user_id')
                                ->label('Nouveau propriétaire')
                                ->options(User::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(fn ($record) => $record->update(['user_id' => $data['new_user_id']]));
                            Notification::make()
                                ->title('Projets transférés')
                                ->body(count($records).' projet(s) transféré(s).')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make()
                        ->label('Supprimer')
                        ->modalHeading('Supprimer les projets')
                        ->modalDescription('Cette action supprimera les projets sélectionnés ainsi que tous leurs objets et configurations de pièce.')
                        ->modalSubmitActionLabel('Oui, supprimer'),
                ]),
            ])
            ->emptyStateHeading('Aucun projet')
            ->emptyStateDescription('Les projets des utilisateurs apparaîtront ici.')
            ->emptyStateIcon('heroicon-o-folder')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
}
