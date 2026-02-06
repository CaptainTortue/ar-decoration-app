<?php

namespace App\Filament\Admin\Resources\ProjectResource\Pages;

use App\Filament\Admin\Resources\ProjectResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    public function getTitle(): string
    {
        return $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $status = match($this->record->status) {
            'draft' => 'Brouillon',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            default => $this->record->status,
        };

        return 'Propriétaire : ' . $this->record->user->name . ' | ' .
               $this->record->projectObjects->count() . ' objet(s) | ' .
               'Statut: ' . $status;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('transfer')
                ->label('Transférer')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('info')
                ->form([
                    Select::make('new_user_id')
                        ->label('Nouveau propriétaire')
                        ->options(User::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->default($this->record->user_id),
                ])
                ->action(function (array $data) {
                    $oldUser = $this->record->user->name;
                    $newUser = User::find($data['new_user_id'])->name;
                    $this->record->update(['user_id' => $data['new_user_id']]);

                    Notification::make()
                        ->title('Projet transféré')
                        ->body("Le projet a été transféré de {$oldUser} à {$newUser}.")
                        ->success()
                        ->send();
                }),
            Actions\EditAction::make()
                ->label('Modifier')
                ->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()
                ->label('Supprimer')
                ->icon('heroicon-o-trash')
                ->modalHeading('Supprimer le projet')
                ->modalDescription(fn () => "Cette action supprimera le projet \"{$this->record->name}\" ainsi que " . $this->record->projectObjects->count() . " objet(s) et la configuration de la pièce."),
        ];
    }
}
