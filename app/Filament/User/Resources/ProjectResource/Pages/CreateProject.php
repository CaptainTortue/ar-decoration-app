<?php

namespace App\Filament\User\Resources\ProjectResource\Pages;

use App\Filament\User\Resources\ProjectResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    public function getTitle(): string
    {
        return 'Créer un nouveau projet';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Projet créé')
            ->body('Votre projet "'.$this->record->name.'" a été créé avec succès. Vous pouvez maintenant ajouter des objets et configurer la pièce.');
    }
}
