<?php

namespace App\Filament\User\Resources\ProjectResource\Pages;

use App\Filament\User\Resources\ProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    public function getTitle(): string
    {
        return 'Modifier : '.$this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Voir')
                ->icon('heroicon-o-eye'),
            DeleteAction::make()
                ->label('Supprimer')
                ->icon('heroicon-o-trash')
                ->modalHeading('Supprimer le projet')
                ->modalDescription(fn () => "Êtes-vous sûr de vouloir supprimer le projet \"{$this->record->name}\" ? Cette action supprimera également ".$this->record->projectObjects->count().' objet(s) et la configuration de la pièce.')
                ->modalSubmitActionLabel('Oui, supprimer'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Modifications enregistrées')
            ->body('Les modifications du projet "'.$this->record->name.'" ont été enregistrées.');
    }
}
