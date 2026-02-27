<?php

namespace App\Filament\User\Resources\ProjectResource\Pages;

use App\Filament\User\Resources\ProjectResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    /**
     * Écoute les événements émis par les RelationManagers (Room, ProjectObjects)
     * après chaque création / modification / suppression.
     * Recharge le record depuis la DB pour que getSubheading() ait des données fraîches.
     */
    #[On('project-data-updated')]
    public function refreshProjectData(): void
    {
        $this->record->refresh();
        $this->record->load('room', 'projectObjects');
    }

    public function getTitle(): string
    {
        return $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $status = match ($this->record->status) {
            'draft' => 'Brouillon',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            default => $this->record->status,
        };

        $roomInfo = $this->record->room
            ? "Pièce: {$this->record->room->width}m × {$this->record->room->length}m"
            : 'Pièce non configurée';

        return $this->record->projectObjects->count().' objet(s) | '.$status.' | '.$roomInfo;
    }

    protected function getHeaderActions(): array
    {
        return [
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
                        ->default($this->record->status)
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => $data['status']]);

                    Notification::make()
                        ->title('Statut modifié')
                        ->success()
                        ->send();

                    $this->fillForm(); // Recharge les données du formulaire avec les nouvelles valeurs
                }),
            EditAction::make()
                ->label('Modifier')
                ->icon('heroicon-o-pencil'),
            DeleteAction::make()
                ->label('Supprimer')
                ->icon('heroicon-o-trash')
                ->modalHeading('Supprimer le projet')
                ->modalDescription(fn () => "Êtes-vous sûr de vouloir supprimer le projet \"{$this->record->name}\" ? Cette action supprimera également ".$this->record->projectObjects->count().' objet(s) et la configuration de la pièce.')
                ->modalSubmitActionLabel('Oui, supprimer'),
        ];
    }
}
