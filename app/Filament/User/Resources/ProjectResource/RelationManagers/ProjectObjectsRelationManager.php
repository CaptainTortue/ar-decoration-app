<?php

namespace App\Filament\User\Resources\ProjectResource\RelationManagers;

use App\Models\FurnitureObject;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectObjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectObjects';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return 'Objets du projet (' . $ownerRecord->projectObjects->count() . ')';
    }

    public static function getModelLabel(): string
    {
        return 'Objet';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Objets';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sélection de l\'objet')
                    ->description('Choisissez l\'objet 3D à ajouter au projet')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        Select::make('furniture_object_id')
                            ->label('Objet 3D')
                            ->relationship('furnitureObject', 'name')
                            ->getOptionLabelFromRecordUsing(fn (FurnitureObject $record) => "{$record->name} ({$record->category->name})")
                            ->searchable(['name', 'description'])
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $object = FurnitureObject::find($state);
                                    if ($object) {
                                        $set('scale_x', $object->default_scale ?? 1);
                                        $set('scale_y', $object->default_scale ?? 1);
                                        $set('scale_z', $object->default_scale ?? 1);
                                    }
                                }
                            }),
                        Placeholder::make('object_info')
                            ->label('Informations')
                            ->content(function (Get $get) {
                                $objectId = $get('furniture_object_id');
                                if (!$objectId) {
                                    return 'Sélectionnez un objet pour voir ses informations.';
                                }
                                $object = FurnitureObject::find($objectId);
                                if (!$object) {
                                    return '';
                                }
                                return "Dimensions: {$object->width}m × {$object->depth}m × {$object->height}m";
                            })
                            ->visible(fn (Get $get) => $get('furniture_object_id') !== null),
                    ]),

                Section::make('Position dans l\'espace')
                    ->description('Coordonnées X, Y, Z de l\'objet dans la scène')
                    ->icon('heroicon-o-arrows-pointing-out')
                    ->schema([
                        TextInput::make('position_x')
                            ->label('Position X')
                            ->numeric()
                            ->step(0.001)
                            ->default(0)
                            ->suffix('m'),
                        TextInput::make('position_y')
                            ->label('Position Y')
                            ->numeric()
                            ->step(0.001)
                            ->default(0)
                            ->suffix('m'),
                        TextInput::make('position_z')
                            ->label('Position Z')
                            ->numeric()
                            ->step(0.001)
                            ->default(0)
                            ->suffix('m'),
                    ])
                    ->columns(3)
                    ->collapsed()
                    ->collapsible(),

                Section::make('Rotation')
                    ->description('Angles de rotation en degrés')
                    ->icon('heroicon-o-arrow-path')
                    ->schema([
                        TextInput::make('rotation_x')
                            ->label('Rotation X')
                            ->numeric()
                            ->step(0.001)
                            ->default(0)
                            ->suffix('°'),
                        TextInput::make('rotation_y')
                            ->label('Rotation Y')
                            ->numeric()
                            ->step(0.001)
                            ->default(0)
                            ->suffix('°'),
                        TextInput::make('rotation_z')
                            ->label('Rotation Z')
                            ->numeric()
                            ->step(0.001)
                            ->default(0)
                            ->suffix('°'),
                    ])
                    ->columns(3)
                    ->collapsed()
                    ->collapsible(),

                Section::make('Échelle')
                    ->description('Facteur de mise à l\'échelle')
                    ->icon('heroicon-o-arrows-pointing-in')
                    ->schema([
                        TextInput::make('scale_x')
                            ->label('Échelle X')
                            ->numeric()
                            ->step(0.001)
                            ->default(1)
                            ->minValue(0.001),
                        TextInput::make('scale_y')
                            ->label('Échelle Y')
                            ->numeric()
                            ->step(0.001)
                            ->default(1)
                            ->minValue(0.001),
                        TextInput::make('scale_z')
                            ->label('Échelle Z')
                            ->numeric()
                            ->step(0.001)
                            ->default(1)
                            ->minValue(0.001),
                    ])
                    ->columns(3)
                    ->collapsed()
                    ->collapsible(),

                Section::make('Apparence')
                    ->description('Personnalisez l\'apparence de l\'objet')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        TextInput::make('color')
                            ->label('Couleur (hex ou nom)')
                            ->placeholder('#FF5733 ou red'),
                        TextInput::make('material')
                            ->label('Matériau')
                            ->placeholder('Ex: bois, métal, tissu...'),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->collapsible(),

                Section::make('Options')
                    ->description('Contrôlez la visibilité et le verrouillage')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Toggle::make('is_visible')
                            ->label('Visible')
                            ->helperText('L\'objet sera-t-il affiché dans la scène ?')
                            ->default(true)
                            ->inline(false),
                        Toggle::make('is_locked')
                            ->label('Verrouillé')
                            ->helperText('Empêche les modifications accidentelles de position/rotation')
                            ->default(false)
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('furnitureObject.name')
                    ->label('Objet')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->furnitureObject->category->name ?? ''),
                TextColumn::make('position')
                    ->label('Position')
                    ->getStateUsing(fn ($record) => sprintf('(%.2f, %.2f, %.2f)', $record->position_x, $record->position_y, $record->position_z))
                    ->fontFamily('mono')
                    ->size('sm'),
                TextColumn::make('scale')
                    ->label('Échelle')
                    ->getStateUsing(fn ($record) => sprintf('(%.2f, %.2f, %.2f)', $record->scale_x, $record->scale_y, $record->scale_z))
                    ->fontFamily('mono')
                    ->size('sm'),
                TextColumn::make('color')
                    ->label('Couleur')
                    ->placeholder('Par défaut')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),
                IconColumn::make('is_locked')
                    ->label('Verrouillé')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->filters([
                SelectFilter::make('furniture_object_id')
                    ->label('Type d\'objet')
                    ->relationship('furnitureObject', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_visible')
                    ->label('Visibilité'),
                TernaryFilter::make('is_locked')
                    ->label('Verrouillage'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Ajouter un objet')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Ajouter un objet au projet')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Objet ajouté')
                            ->body('L\'objet a été ajouté au projet.')
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Modifier')
                        ->modalHeading(fn ($record) => 'Modifier : ' . $record->furnitureObject->name),
                    Action::make('toggle_visibility')
                        ->label(fn ($record) => $record->is_visible ? 'Masquer' : 'Afficher')
                        ->icon(fn ($record) => $record->is_visible ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color('gray')
                        ->action(function ($record) {
                            $record->update(['is_visible' => !$record->is_visible]);
                            Notification::make()
                                ->title($record->is_visible ? 'Objet visible' : 'Objet masqué')
                                ->success()
                                ->send();
                        }),
                    Action::make('toggle_lock')
                        ->label(fn ($record) => $record->is_locked ? 'Déverrouiller' : 'Verrouiller')
                        ->icon(fn ($record) => $record->is_locked ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed')
                        ->color('warning')
                        ->action(function ($record) {
                            $record->update(['is_locked' => !$record->is_locked]);
                            Notification::make()
                                ->title($record->is_locked ? 'Objet verrouillé' : 'Objet déverrouillé')
                                ->success()
                                ->send();
                        }),
                    Action::make('reset_transform')
                        ->label('Réinitialiser la transformation')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Réinitialiser la transformation')
                        ->modalDescription('Position, rotation et échelle seront remises à leurs valeurs par défaut.')
                        ->action(function ($record) {
                            $record->update([
                                'position_x' => 0,
                                'position_y' => 0,
                                'position_z' => 0,
                                'rotation_x' => 0,
                                'rotation_y' => 0,
                                'rotation_z' => 0,
                                'scale_x' => 1,
                                'scale_y' => 1,
                                'scale_z' => 1,
                            ]);
                            Notification::make()
                                ->title('Transformation réinitialisée')
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make()
                        ->label('Retirer du projet')
                        ->modalHeading('Retirer l\'objet du projet')
                        ->modalDescription(fn ($record) => "Voulez-vous retirer \"{$record->furnitureObject->name}\" du projet ? Cette action ne supprime pas l'objet du catalogue, seulement son placement dans ce projet.")
                        ->modalSubmitActionLabel('Oui, retirer'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Actions'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_toggle_visibility')
                        ->label('Basculer la visibilité')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['is_visible' => !$record->is_visible]));
                            Notification::make()
                                ->title('Visibilité modifiée')
                                ->body(count($records) . ' objet(s) mis à jour.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('bulk_lock')
                        ->label('Verrouiller')
                        ->icon('heroicon-o-lock-closed')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['is_locked' => true]));
                            Notification::make()
                                ->title('Objets verrouillés')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('bulk_unlock')
                        ->label('Déverrouiller')
                        ->icon('heroicon-o-lock-open')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['is_locked' => false]));
                            Notification::make()
                                ->title('Objets déverrouillés')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make()
                        ->label('Retirer du projet')
                        ->modalHeading('Retirer les objets sélectionnés')
                        ->modalDescription('Voulez-vous retirer les objets sélectionnés du projet ? Cette action est irréversible.')
                        ->modalSubmitActionLabel('Oui, retirer'),
                ]),
            ])
            ->emptyStateHeading('Aucun objet dans ce projet')
            ->emptyStateDescription('Ajoutez des objets 3D à votre projet pour commencer votre décoration.')
            ->emptyStateIcon('heroicon-o-cube')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Ajouter un objet')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('created_at', 'desc')
            ->reorderable('position_y');
    }
}
