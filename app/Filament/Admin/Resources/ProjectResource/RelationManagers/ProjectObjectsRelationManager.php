<?php

namespace App\Filament\Admin\Resources\ProjectResource\RelationManagers;

use App\Models\FurnitureObject;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                    ->icon('heroicon-o-cube')
                    ->schema([
                        Select::make('furniture_object_id')
                            ->label('Objet 3D')
                            ->relationship('furnitureObject', 'name')
                            ->getOptionLabelFromRecordUsing(fn (FurnitureObject $record) => "{$record->name} ({$record->category->name})")
                            ->searchable(['name', 'description'])
                            ->preload()
                            ->required()
                            ->live(),
                        Placeholder::make('object_info')
                            ->label('Dimensions')
                            ->content(function (Get $get) {
                                $objectId = $get('furniture_object_id');
                                if (!$objectId) return 'Sélectionnez un objet';
                                $object = FurnitureObject::find($objectId);
                                if (!$object) return '';
                                return "{$object->width}m × {$object->depth}m × {$object->height}m";
                            })
                            ->visible(fn (Get $get) => $get('furniture_object_id') !== null),
                    ]),

                Section::make('Position')
                    ->icon('heroicon-o-arrows-pointing-out')
                    ->schema([
                        TextInput::make('position_x')->label('X')->numeric()->step(0.001)->default(0)->suffix('m'),
                        TextInput::make('position_y')->label('Y')->numeric()->step(0.001)->default(0)->suffix('m'),
                        TextInput::make('position_z')->label('Z')->numeric()->step(0.001)->default(0)->suffix('m'),
                    ])
                    ->columns(3)
                    ->collapsed()
                    ->collapsible(),

                Section::make('Rotation')
                    ->icon('heroicon-o-arrow-path')
                    ->schema([
                        TextInput::make('rotation_x')->label('X')->numeric()->step(0.001)->default(0)->suffix('°'),
                        TextInput::make('rotation_y')->label('Y')->numeric()->step(0.001)->default(0)->suffix('°'),
                        TextInput::make('rotation_z')->label('Z')->numeric()->step(0.001)->default(0)->suffix('°'),
                    ])
                    ->columns(3)
                    ->collapsed()
                    ->collapsible(),

                Section::make('Échelle')
                    ->icon('heroicon-o-arrows-pointing-in')
                    ->schema([
                        TextInput::make('scale_x')->label('X')->numeric()->step(0.001)->default(1)->minValue(0.001),
                        TextInput::make('scale_y')->label('Y')->numeric()->step(0.001)->default(1)->minValue(0.001),
                        TextInput::make('scale_z')->label('Z')->numeric()->step(0.001)->default(1)->minValue(0.001),
                    ])
                    ->columns(3)
                    ->collapsed()
                    ->collapsible(),

                Section::make('Apparence')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        TextInput::make('color')->label('Couleur'),
                        TextInput::make('material')->label('Matériau'),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->collapsible(),

                Section::make('Options')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Toggle::make('is_visible')->label('Visible')->default(true)->inline(false),
                        Toggle::make('is_locked')->label('Verrouillé')->default(false)->inline(false),
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
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Modifier'),
                    Action::make('toggle_visibility')
                        ->label(fn ($record) => $record->is_visible ? 'Masquer' : 'Afficher')
                        ->icon(fn ($record) => $record->is_visible ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->action(function ($record) {
                            $record->update(['is_visible' => !$record->is_visible]);
                        }),
                    Action::make('toggle_lock')
                        ->label(fn ($record) => $record->is_locked ? 'Déverrouiller' : 'Verrouiller')
                        ->icon(fn ($record) => $record->is_locked ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed')
                        ->action(function ($record) {
                            $record->update(['is_locked' => !$record->is_locked]);
                        }),
                    DeleteAction::make()
                        ->label('Retirer')
                        ->modalHeading('Retirer l\'objet')
                        ->modalDescription(fn ($record) => "Retirer \"{$record->furnitureObject->name}\" du projet ?"),
                ])
                ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_show')
                        ->label('Afficher')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each(fn ($r) => $r->update(['is_visible' => true])))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('bulk_hide')
                        ->label('Masquer')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn ($records) => $records->each(fn ($r) => $r->update(['is_visible' => false])))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make()
                        ->label('Retirer la sélection'),
                ]),
            ])
            ->emptyStateHeading('Aucun objet')
            ->emptyStateDescription('Ajoutez des objets 3D à ce projet.')
            ->emptyStateIcon('heroicon-o-cube')
            ->defaultSort('created_at', 'desc');
    }
}
