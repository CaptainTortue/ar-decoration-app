<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestUsers extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),
                IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-user')
                    ->trueColor('danger')
                    ->falseColor('gray'),
                TextColumn::make('projects_count')
                    ->label('Projets')
                    ->counts('projects')
                    ->badge()
                    ->color('info'),
                IconColumn::make('email_verified_at')
                    ->label('Vérifié')
                    ->boolean()
                    ->getStateUsing(fn (User $record) => $record->email_verified_at !== null)
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('warning'),
                TextColumn::make('created_at')
                    ->label('Inscrit')
                    ->since()
                    ->sortable(),
            ])
            ->heading('Derniers utilisateurs inscrits')
            ->headerActions([
                Action::make('view_all')
                    ->label('Voir tous les utilisateurs')
                    ->url(UserResource::getUrl('index'))
                    ->icon('heroicon-o-arrow-right')
                    ->color('gray'),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Modifier')
                    ->url(fn (User $record): string => UserResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-pencil')
                    ->color('gray'),
            ])
            ->emptyStateHeading('Aucun utilisateur')
            ->emptyStateIcon('heroicon-o-users')
            ->paginated(false);
    }
}
