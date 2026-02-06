<?php

namespace App\Filament\Admin\Resources\FurnitureObjectResource\Pages;

use App\Filament\Admin\Resources\FurnitureObjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFurnitureObject extends EditRecord
{
    protected static string $resource = FurnitureObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
