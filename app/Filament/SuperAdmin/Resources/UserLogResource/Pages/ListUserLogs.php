<?php

namespace App\Filament\SuperAdmin\Resources\UserLogResource\Pages;

use App\Filament\SuperAdmin\Resources\UserLogResource;
use Filament\Resources\Pages\ListRecords;

class ListUserLogs extends ListRecords
{
    protected static string $resource = UserLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

