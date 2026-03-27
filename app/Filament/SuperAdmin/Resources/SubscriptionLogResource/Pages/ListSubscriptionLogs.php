<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionLogResource\Pages;

use App\Filament\SuperAdmin\Resources\SubscriptionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListSubscriptionLogs extends ListRecords
{
    protected static string $resource = SubscriptionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    $query = $this->getFilteredTableQuery();
                    $fileName = 'subscription-logs-' . now()->format('Y-m-d_His') . '.csv';

                    return response()->streamDownload(function () use ($query) {
                        $handle = fopen('php://output', 'w');

                        fputcsv($handle, [
                            'Business/Client Name',
                            'Subscription Plan',
                            'Start Date',
                            'End Date',
                            'Payment Status',
                            'Date Paid',
                        ]);

                        $query
                            ->orderByDesc('starts_at')
                            ->chunk(500, function ($records) use ($handle) {
                                foreach ($records as $record) {
                                    $paidAt = $record->paid_at ? $record->paid_at->format('Y-m-d H:i:s') : '';

                                    fputcsv($handle, [
                                        (string) $record->business_name,
                                        (string) $record->subscription_plan,
                                        $record->starts_at ? $record->starts_at->format('Y-m-d') : '',
                                        $record->ends_at ? $record->ends_at->format('Y-m-d') : '',
                                        (string) $record->payment_status,
                                        $paidAt,
                                    ]);
                                }
                            });

                        fclose($handle);
                    }, $fileName, [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                        'Content-Disposition' => 'attachment; filename="' . Str::of($fileName)->replace('"', '') . '"',
                    ]);
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
