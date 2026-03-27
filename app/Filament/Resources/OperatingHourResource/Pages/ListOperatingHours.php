<?php

namespace App\Filament\Resources\OperatingHourResource\Pages;

use App\Filament\Resources\OperatingHourResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOperatingHours extends ListRecords
{
    protected static string $resource = OperatingHourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
