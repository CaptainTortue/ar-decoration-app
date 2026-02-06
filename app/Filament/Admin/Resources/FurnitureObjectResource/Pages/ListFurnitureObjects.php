<?php

namespace App\Filament\Admin\Resources\FurnitureObjectResource\Pages;

use App\Filament\Admin\Resources\FurnitureObjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFurnitureObjects extends ListRecords
{
    protected static string $resource = FurnitureObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
