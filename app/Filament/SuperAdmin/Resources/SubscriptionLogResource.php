<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\SubscriptionLogResource\Pages;
use App\Models\Admin;
use App\Models\SubscriptionLog;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionLogResource extends Resource
{
    protected static ?string $model = SubscriptionLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Subscription Logs';

    protected static ?string $navigationGroup = 'Subscriptions';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Record')
                    ->schema([
                        Forms\Components\TextInput::make('business_name')
                            ->disabled(),
                        Forms\Components\TextInput::make('subscription_plan')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\DatePicker::make('starts_at')
                            ->disabled(),
                        Forms\Components\DatePicker::make('ends_at')
                            ->disabled(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Payment')
                    ->schema([
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'PAID' => 'PAID',
                                'PENDING' => 'PENDING',
                                'EXPIRED' => 'EXPIRED',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->seconds(false)
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->defaultSort('starts_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('business_name')
                    ->label('Business/Client')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('subscription_plan')
                    ->label('Plan')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Start Date')
                    ->date('M j, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('End Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->color(function (SubscriptionLog $record): string {
                        if ($record->ends_at && $record->ends_at->isPast()) {
                            return 'danger';
                        }

                        if ($record->ends_at && $record->ends_at->copy()->startOfDay()->lte(now()->addDays(7)->startOfDay())) {
                            return 'warning';
                        }

                        return 'success';
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->formatStateUsing(function (?string $state, SubscriptionLog $record): string {
                        if ($record->ends_at && $record->ends_at->isPast()) {
                            return 'EXPIRED';
                        }

                        return $state ?: 'PENDING';
                    })
                    ->color(function (?string $state, SubscriptionLog $record): string {
                        if ($record->ends_at && $record->ends_at->isPast()) {
                            return 'danger';
                        }

                        return match (strtoupper((string) $state)) {
                            'PAID' => 'success',
                            'PENDING' => 'warning',
                            'EXPIRED' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Date Paid')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('month')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->options(function (): array {
                                $options = [];
                                for ($i = 0; $i < 24; $i++) {
                                    $month = now()->copy()->subMonths($i)->startOfMonth();
                                    $options[$month->format('Y-m')] = $month->format('M Y');
                                }

                                return $options;
                            })
                            ->searchable()
                            ->placeholder('All months'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $month = $data['month'] ?? null;
                        if (! $month) {
                            return $query;
                        }

                        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                        $end = $start->copy()->endOfMonth();

                        return $query->whereBetween('starts_at', [$start->toDateString(), $end->toDateString()]);
                    }),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'PAID' => 'PAID',
                        'PENDING' => 'PENDING',
                        'EXPIRED' => 'EXPIRED',
                    ]),
                Tables\Filters\SelectFilter::make('admin_id')
                    ->label('Client/Business')
                    ->options(function (): array {
                        return Admin::query()
                            ->orderBy('company_name')
                            ->orderBy('name')
                            ->get(['id', 'company_name', 'name'])
                            ->mapWithKeys(fn (Admin $admin) => [$admin->id => ($admin->company_name ?: $admin->name)])
                            ->all();
                    })
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('mark_paid')
                        ->label('Mark as PAID')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->visible(fn (SubscriptionLog $record): bool => $record->payment_status !== 'PAID')
                        ->action(function (SubscriptionLog $record): void {
                            $record->update([
                                'payment_status' => 'PAID',
                                'paid_at' => $record->paid_at ?? now(),
                            ]);
                        }),
                    Tables\Actions\Action::make('mark_pending')
                        ->label('Mark as PENDING')
                        ->icon('heroicon-o-clock')
                        ->requiresConfirmation()
                        ->visible(fn (SubscriptionLog $record): bool => $record->payment_status !== 'PENDING')
                        ->action(function (SubscriptionLog $record): void {
                            $record->update([
                                'payment_status' => 'PENDING',
                                'paid_at' => null,
                            ]);
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptionLogs::route('/'),
            'edit' => Pages\EditSubscriptionLog::route('/{record}/edit'),
        ];
    }
}
