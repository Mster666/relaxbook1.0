<?php

namespace App\Filament\Resources\OperatingHourResource\Pages;

use App\Filament\Resources\OperatingHourResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOperatingHour extends EditRecord
{
    protected static string $resource = OperatingHourResource::class;

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
