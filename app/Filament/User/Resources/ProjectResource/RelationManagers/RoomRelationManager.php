<?php

namespace App\Filament\User\Resources\ProjectResource\RelationManagers;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
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
                        Grid::make(3)
                            ->schema([
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
                            ]),
                    ]),

                Section::make('Configuration du sol')
                    ->description('Apparence du sol de la pièce')
                    ->icon('heroicon-o-square-3-stack-3d')
                    ->schema([
                        Grid::make(2)
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
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Configuration des murs')
                    ->description('Apparence des murs de la pièce')
                    ->icon('heroicon-o-rectangle-group')
                    ->schema([
                        Grid::make(2)
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
                            ]),
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->weight('bold')
                    ->icon('heroicon-o-home'),
                Tables\Columns\TextColumn::make('dimensions')
                    ->label('Dimensions')
                    ->getStateUsing(fn ($record) => "{$record->width}m × {$record->length}m × {$record->height}m")
                    ->fontFamily('mono')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('surface')
                    ->label('Surface')
                    ->getStateUsing(fn ($record) => number_format($record->width * $record->length, 2) . ' m²')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('floor_material')
                    ->label('Sol')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'wood' => 'Parquet bois',
                        'laminate' => 'Stratifié',
                        'tile' => 'Carrelage',
                        'carpet' => 'Moquette',
                        'concrete' => 'Béton',
                        'marble' => 'Marbre',
                        'vinyl' => 'Vinyle',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('wall_material')
                    ->label('Murs')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paint' => 'Peinture',
                        'wallpaper' => 'Papier peint',
                        'brick' => 'Brique',
                        'stone' => 'Pierre',
                        'wood_panel' => 'Lambris bois',
                        'concrete' => 'Béton',
                        'plaster' => 'Plâtre',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Configurer la pièce')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Configurer la pièce')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Pièce configurée')
                            ->body('La configuration de la pièce a été enregistrée.')
                    )
                    ->visible(fn () => $this->ownerRecord->room === null),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Modifier')
                        ->modalHeading(fn ($record) => 'Modifier : ' . $record->name),
                    Tables\Actions\Action::make('apply_preset')
                        ->label('Appliquer un modèle')
                        ->icon('heroicon-o-rectangle-stack')
                        ->color('info')
                        ->form([
                            Select::make('preset')
                                ->label('Modèle de pièce')
                                ->options([
                                    'living_room' => 'Salon standard (5m × 4m)',
                                    'bedroom' => 'Chambre standard (4m × 3.5m)',
                                    'office' => 'Bureau (3m × 3m)',
                                    'kitchen' => 'Cuisine (4m × 3m)',
                                    'bathroom' => 'Salle de bain (2.5m × 2m)',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function ($record, array $data) {
                            $presets = [
                                'living_room' => ['width' => 5, 'length' => 4, 'height' => 2.5, 'floor_material' => 'wood', 'wall_material' => 'paint'],
                                'bedroom' => ['width' => 4, 'length' => 3.5, 'height' => 2.5, 'floor_material' => 'laminate', 'wall_material' => 'wallpaper'],
                                'office' => ['width' => 3, 'length' => 3, 'height' => 2.5, 'floor_material' => 'laminate', 'wall_material' => 'paint'],
                                'kitchen' => ['width' => 4, 'length' => 3, 'height' => 2.5, 'floor_material' => 'tile', 'wall_material' => 'paint'],
                                'bathroom' => ['width' => 2.5, 'length' => 2, 'height' => 2.4, 'floor_material' => 'tile', 'wall_material' => 'tile'],
                            ];

                            $record->update($presets[$data['preset']]);

                            Notification::make()
                                ->title('Modèle appliqué')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->label('Supprimer la configuration')
                        ->modalHeading('Supprimer la configuration de la pièce')
                        ->modalDescription('Voulez-vous supprimer la configuration de la pièce ? Les objets du projet ne seront pas affectés, mais vous perdrez les dimensions et paramètres de la pièce.')
                        ->modalSubmitActionLabel('Oui, supprimer'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Actions'),
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateHeading('Aucune pièce configurée')
            ->emptyStateDescription('Configurez les dimensions et l\'apparence de votre pièce pour une meilleure visualisation.')
            ->emptyStateIcon('heroicon-o-home')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Configurer la pièce')
                    ->icon('heroicon-o-plus'),
            ])
            ->paginated(false);
    }
}
