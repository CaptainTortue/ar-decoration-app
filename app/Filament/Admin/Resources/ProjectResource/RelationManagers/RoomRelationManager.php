<?php

namespace App\Filament\Admin\Resources\ProjectResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoomRelationManager extends RelationManager
{
    protected static string $relationship = 'room';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return $ownerRecord->room ? 'Pièce configurée' : 'Configuration de la pièce';
    }

    public static function getModelLabel(): string
    {
        return 'Pièce';
    }

    // Toujours éditable, que ce soit sur la page View ou Edit
    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations de base')
                    ->description('Donnez un nom à votre pièce')
                    ->icon('heroicon-o-home')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom de la pièce')
                            ->placeholder('Ex: Salon, Chambre, Bureau...')
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Dimensions de la pièce')
                    ->description('Définissez les dimensions en mètres')
                    ->icon('heroicon-o-arrows-pointing-out')
                    ->schema([
                        FusedGroup::make([
                            TextInput::make('width')
                                ->label('Largeur')
                                ->numeric()
                                ->step(0.01)
                                ->required()
                                ->suffix('m')
                                ->minValue(0.5)
                                ->maxValue(50)
                                ->placeholder('4.00'),
                            TextInput::make('length')
                                ->label('Longueur')
                                ->numeric()
                                ->step(0.01)
                                ->required()
                                ->suffix('m')
                                ->minValue(0.5)
                                ->maxValue(50)
                                ->placeholder('5.00'),
                            TextInput::make('height')
                                ->label('Hauteur')
                                ->numeric()
                                ->step(0.01)
                                ->required()
                                ->suffix('m')
                                ->minValue(1.5)
                                ->maxValue(10)
                                ->default(2.50)
                                ->placeholder('2.50'),
                        ])
                            ->label('Dimenssion')
                            ->columns(3),
                    ]),

                Section::make('Configuration du sol')
                    ->description('Apparence du sol de la pièce')
                    ->icon('heroicon-o-square-3-stack-3d')
                    ->schema([
                        Select::make('floor_material')
                            ->label('Matériau du sol')
                            ->options([
                                'wood' => 'Parquet bois',
                                'laminate' => 'Stratifié',
                                'tile' => 'Carrelage',
                                'carpet' => 'Moquette',
                                'concrete' => 'Béton',
                                'marble' => 'Marbre',
                                'vinyl' => 'Vinyle',
                            ])
                            ->default('wood')
                            ->native(false)
                            ->searchable(),
                        TextInput::make('floor_color')
                            ->label('Couleur du sol')
                            ->placeholder('#C4A882')
                            ->default('#C4A882'),
                    ])
                    ->collapsible(),

                Section::make('Configuration des murs')
                    ->description('Apparence des murs de la pièce')
                    ->icon('heroicon-o-rectangle-group')
                    ->schema([
                        Select::make('wall_material')
                            ->label('Matériau des murs')
                            ->options([
                                'paint' => 'Peinture',
                                'wallpaper' => 'Papier peint',
                                'brick' => 'Brique',
                                'stone' => 'Pierre',
                                'wood_panel' => 'Lambris bois',
                                'concrete' => 'Béton',
                                'plaster' => 'Plâtre',
                            ])
                            ->default('paint')
                            ->native(false)
                            ->searchable(),
                        TextInput::make('wall_color')
                            ->label('Couleur des murs')
                            ->placeholder('#FFFFFF')
                            ->default('#FFFFFF'),
                    ])
                    ->collapsible(),

                Section::make('Paramètres d\'éclairage')
                    ->description('Configuration avancée de l\'éclairage de la scène')
                    ->icon('heroicon-o-light-bulb')
                    ->schema([
                        KeyValue::make('lighting_settings')
                            ->label('')
                            ->keyLabel('Paramètre')
                            ->valueLabel('Valeur')
                            ->addActionLabel('Ajouter un paramètre')
                            ->keyPlaceholder('Ex: ambient_intensity')
                            ->valuePlaceholder('Ex: 0.5')
                            ->reorderable()
                            ->default([
                                'ambient_intensity' => '0.5',
                                'directional_intensity' => '0.8',
                                'shadow_enabled' => 'true',
                            ]),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    final public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->weight('bold')
                    ->icon('heroicon-o-home'),
                TextColumn::make('dimensions')
                    ->label('Dimensions')
                    ->getStateUsing(fn ($record) => "{$record->width}m × {$record->length}m × {$record->height}m")
                    ->badge()
                    ->color('info'),
                TextColumn::make('surface')
                    ->label('Surface')
                    ->getStateUsing(fn ($record) => number_format($record->width * $record->length, 2).' m²')
                    ->badge()
                    ->color('success'),
                TextColumn::make('floor_material')
                    ->label('Sol')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'wood' => 'Parquet',
                        'laminate' => 'Stratifié',
                        'tile' => 'Carrelage',
                        'carpet' => 'Moquette',
                        'concrete' => 'Béton',
                        'marble' => 'Marbre',
                        'vinyl' => 'Vinyle',
                        default => $state,
                    }),
                TextColumn::make('wall_material')
                    ->label('Murs')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paint' => 'Peinture',
                        'wallpaper' => 'Papier peint',
                        'brick' => 'Brique',
                        'stone' => 'Pierre',
                        'wood_panel' => 'Lambris',
                        'concrete' => 'Béton',
                        'plaster' => 'Plâtre',
                        default => $state,
                    }),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->label('Configurer la pièce')
                    ->icon('heroicon-o-plus')
                    ->visible(fn () => $this->ownerRecord->room === null),
            ])
            ->actions([
                EditAction::make()
                    ->label('Modifier'),
                DeleteAction::make()
                    ->label('Supprimer')
                    ->modalHeading('Supprimer la pièce')
                    ->modalDescription('La configuration de la pièce sera supprimée. Les objets du projet ne seront pas affectés.'),
            ])
            ->bulkActions([])
            ->emptyStateHeading('Aucune pièce configurée')
            ->emptyStateIcon('heroicon-o-home')
            ->paginated(false);
    }
}
