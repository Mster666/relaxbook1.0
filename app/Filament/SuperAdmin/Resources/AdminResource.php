<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\AdminResource\Pages;
use App\Models\Admin;
use App\Filament\SuperAdmin\Resources\SubscriptionLogResource;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Admins';

    protected static ?string $navigationGroup = 'Account Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\TextInput::make('company_name')
                            ->label('Company Name')
                            ->maxLength(191),
                        Forms\Components\FileUpload::make('company_logo')
                            ->label('Company Logo')
                            ->image()
                            ->disk('public')
                            ->directory('company-logos')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('company_address')
                            ->label('Company Address')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('company_latitude')
                            ->label('Company Latitude')
                            ->numeric()
                            ->rule('between:-90,90')
                            ->nullable(),
                        Forms\Components\TextInput::make('company_longitude')
                            ->label('Company Longitude')
                            ->numeric()
                            ->rule('between:-180,180')
                            ->nullable(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(191),
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ]),
                        Forms\Components\TextInput::make('age')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(120),
                        Forms\Components\FileUpload::make('profile_picture')
                            ->image()
                            ->disk('public')
                            ->directory('admins')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Security & Status')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? \Illuminate\Support\Facades\Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Account Active')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true),
                        Forms\Components\Select::make('plan_duration')
                            ->label('Plan Duration')
                            ->options([
                                '1m' => '1 month',
                                '3m' => '3 months',
                                '6m' => '6 months',
                                '9m' => '9 months',
                                '12m' => '1 year',
                            ])
                            ->dehydrated(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                                if (! $state) {
                                    return;
                                }

                                $months = match ((string) $state) {
                                    '1m' => 1,
                                    '3m' => 3,
                                    '6m' => 6,
                                    '9m' => 9,
                                    '12m' => 12,
                                    default => 1,
                                };

                                $startRaw = $get('subscription_verified_at');
                                $start = $startRaw ? Carbon::parse($startRaw) : now();

                                if (! $startRaw) {
                                    $set('subscription_verified_at', $start);
                                }

                                $set('subscription_expires_at', $start->copy()->addMonthsNoOverflow($months)->endOfDay());
                            }),
                        Forms\Components\DateTimePicker::make('subscription_verified_at')
                            ->label('Subscription Start Date')
                            ->seconds(false)
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('subscription_expires_at')
                            ->label('Subscription Expiry Date')
                            ->seconds(false)
                            ->helperText('Leave empty for lifetime access')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                Tables\Columns\ImageColumn::make('profile_picture')
                    ->circular()
                    ->disk('public')
                    ->size(40),
                Tables\Columns\ImageColumn::make('company_logo')
                    ->label('Logo')
                    ->disk('public')
                    ->height(28)
                    ->width(28),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('ratings_avg_rating')
                    ->label('Rating')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state, 1) : '—'),
                Tables\Columns\TextColumn::make('ratings_count')
                    ->label('Ratings')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => (string) ((int) ($state ?? 0))),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription_expires_at')
                    ->label('Subscription')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->color(fn ($state) => ($state && \Carbon\Carbon::parse($state)->isPast()) ? 'danger' : 'success')
                    ->placeholder('Lifetime'),
                Tables\Columns\TextColumn::make('subscription_verified_at')
                    ->label('Date Verified')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Account Status'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('subscribe')
                        ->label('Subscribe / Renew')
                        ->icon('heroicon-o-credit-card')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('plan_duration')
                                ->label('Plan')
                                ->options([
                                    '1m' => '1 month',
                                    '3m' => '3 months',
                                    '6m' => '6 months',
                                    '9m' => '9 months',
                                    '12m' => '1 year',
                                ])
                                ->required(),
                            Forms\Components\DatePicker::make('start_date')
                                ->label('Start Date')
                                ->default(now()->toDateString())
                                ->required(),
                        ])
                        ->action(function (Admin $record, array $data) {
                            $months = match ((string) $data['plan_duration']) {
                                '1m' => 1,
                                '3m' => 3,
                                '6m' => 6,
                                '9m' => 9,
                                '12m' => 12,
                                default => 1,
                            };

                            $start = Carbon::parse($data['start_date'])->startOfDay();
                            $expires = $start->copy()->addMonthsNoOverflow($months)->endOfDay();

                            $record->update([
                                'is_active' => true,
                                'subscription_verified_at' => $start,
                                'subscription_expires_at' => $expires,
                            ]);

                            return redirect()->to(
                                SubscriptionLogResource::getUrl('index', [
                                    'tableFilters' => [
                                        'admin_id' => ['value' => $record->id],
                                    ],
                                ]),
                            );
                        }),
                    Tables\Actions\Action::make('unsubscribe')
                        ->label('Unsubscribe')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Admin $record): void {
                            $record->update([
                                'is_active' => false,
                                'subscription_expires_at' => now()->subMinute(),
                            ]);
                        }),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
