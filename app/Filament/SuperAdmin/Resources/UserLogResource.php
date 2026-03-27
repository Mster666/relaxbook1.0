<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\UserLogResource\Pages;
use App\Models\UserLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserLogResource extends Resource
{
    protected static ?string $model = UserLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'User Logs';

    protected static ?string $navigationGroup = 'Logs';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->wrap(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'login' => 'success',
                        'logout' => 'gray',
                        'profile_updated' => 'info',
                        'password_changed' => 'warning',
                        'booking_created' => 'primary',
                        'booking_cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->toggleable()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap()
                    ->limit(80)
                    ->tooltip(fn (?string $state): ?string => $state ?: null),
                Tables\Columns\TextColumn::make('meta')
                    ->label('Meta')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state): string {
                        if (empty($state)) {
                            return '—';
                        }

                        if (is_string($state)) {
                            return $state;
                        }

                        return json_encode($state, JSON_UNESCAPED_SLASHES) ?: '—';
                    })
                    ->wrap()
                    ->limit(120)
                    ->tooltip(function ($state): ?string {
                        if (empty($state)) {
                            return null;
                        }

                        if (is_string($state)) {
                            return $state;
                        }

                        return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: null;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options(fn (): array => UserLog::query()
                        ->select('action')
                        ->distinct()
                        ->orderBy('action')
                        ->pluck('action', 'action')
                        ->all()
                    ),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, string $date): Builder => $q->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, string $date): Builder => $q->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserLogs::route('/'),
        ];
    }
}

