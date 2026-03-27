<?php

namespace App\Filament\Resources\OperatingHourResource\Pages;

use App\Filament\Resources\OperatingHourResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateOperatingHour extends CreateRecord
{
    protected static string $resource = OperatingHourResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Auth::guard('admin')->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
