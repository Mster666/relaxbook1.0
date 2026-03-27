<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionLogResource\Pages;

use App\Filament\SuperAdmin\Resources\SubscriptionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionLog extends EditRecord
{
    protected static string $resource = SubscriptionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

