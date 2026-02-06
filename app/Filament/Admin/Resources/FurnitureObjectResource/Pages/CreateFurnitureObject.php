<?php

namespace App\Filament\Admin\Resources\FurnitureObjectResource\Pages;

use App\Filament\Admin\Resources\FurnitureObjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFurnitureObject extends CreateRecord
{
    protected static string $resource = FurnitureObjectResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
