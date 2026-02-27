<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FurnitureObjectResource\Pages;
use App\Models\FurnitureObject;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FurnitureObjectResource extends Resource
{
    protected static ?string $model = FurnitureObject::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-cube';
    }

    public static function getModelLabel(): string
    {
        return 'Objet 3D';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Objets 3D';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Objet')
                    ->tabs([
                        Tabs\Tab::make('Informations générales')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nom')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Select::make('category_id')
                                    ->label('Catégorie')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                TextInput::make('price')
                                    ->label('Prix (€)')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01),
                                Toggle::make('is_active')
                                    ->label('Actif')
                                    ->default(true)
                                    ->helperText('Les objets inactifs ne sont pas visibles dans l\'API.'),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Fichiers')
                            ->icon('heroicon-o-document')
                            ->schema([
                                TextInput::make('model_path')
                                    ->label('Chemin du modèle 3D')
                                    ->required()
                                    ->placeholder('models/category/object.glb'),
                                TextInput::make('thumbnail_path')
                                    ->label('Chemin de la miniature')
                                    ->placeholder('thumbnails/category/object.webp'),
                            ]),

                        Tabs\Tab::make('Dimensions')
                            ->icon('heroicon-o-arrows-pointing-out')
                            ->schema([
                                TextInput::make('width')
                                    ->label('Largeur (m)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('m'),
                                TextInput::make('height')
                                    ->label('Hauteur (m)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('m'),
                                TextInput::make('depth')
                                    ->label('Profondeur (m)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('m'),
                                TextInput::make('default_scale')
                                    ->label('Échelle par défaut')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(1.0),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Options')
                            ->icon('heroicon-o-swatch')
                            ->schema([
                                TagsInput::make('available_colors')
                                    ->label('Couleurs disponibles')
                                    ->placeholder('Ajouter une couleur')
                                    ->helperText('Codes hexadécimaux (#FFFFFF) ou noms de couleurs'),
                                TagsInput::make('available_materials')
                                    ->label('Matériaux disponibles')
                                    ->placeholder('Ajouter un matériau'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                ImageColumn::make('thumbnail_path')
                    ->label('Aperçu')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->sortable()
                    ->badge(),
                TextColumn::make('dimensions')
                    ->label('Dimensions')
                    ->getStateUsing(fn ($record) => $record->width && $record->height && $record->depth
                        ? "{$record->width}m × {$record->height}m × {$record->depth}m"
                        : '-'),
                TextColumn::make('price')
                    ->label('Prix')
                    ->money('EUR')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                TextColumn::make('project_objects_count')
                    ->label('Utilisations')
                    ->counts('projectObjects')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_active')
                    ->label('Actif')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFurnitureObjects::route('/'),
            'create' => Pages\CreateFurnitureObject::route('/create'),
            'edit' => Pages\EditFurnitureObject::route('/{record}/edit'),
        ];
    }
}
