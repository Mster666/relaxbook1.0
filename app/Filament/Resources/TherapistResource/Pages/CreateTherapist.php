<?php

namespace App\Filament\Resources\TherapistResource\Pages;

use App\Filament\Resources\TherapistResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTherapist extends CreateRecord
{
    protected static string $resource = TherapistResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Auth::guard('admin')->id();

        return $data;
    }
}
