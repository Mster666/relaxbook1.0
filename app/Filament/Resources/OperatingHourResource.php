<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperatingHourResource\Pages;
use App\Models\AdminOperatingHour;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class OperatingHourResource extends Resource
{
    protected static ?string $model = AdminOperatingHour::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Operating Hours';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        $hasBreaksTable = Schema::hasTable('admin_operating_breaks');

        $schema = [
            Forms\Components\Select::make('day_of_week')
                ->label('Day')
                ->options([
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                    6 => 'Saturday',
                    7 => 'Sunday',
                ])
                ->required()
                ->unique(ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->where('admin_id', Auth::guard('admin')->id())),
            Forms\Components\Toggle::make('is_closed')
                ->label('Closed')
                ->default(false)
                ->live(),
            Forms\Components\TimePicker::make('opens_at')
                ->seconds(false)
                ->required(fn (Forms\Get $get) => ! (bool) $get('is_closed'))
                ->disabled(fn (Forms\Get $get) => (bool) $get('is_closed')),
            Forms\Components\TimePicker::make('closes_at')
                ->seconds(false)
                ->required(fn (Forms\Get $get) => ! (bool) $get('is_closed'))
                ->disabled(fn (Forms\Get $get) => (bool) $get('is_closed')),
        ];

        if ($hasBreaksTable) {
            $schema[] = Forms\Components\Repeater::make('breaks')
                ->relationship()
                ->label('Break Times')
                ->schema([
                    Forms\Components\TextInput::make('label')
                        ->maxLength(120)
                        ->placeholder('Lunch Break'),
                    Forms\Components\TimePicker::make('starts_at')
                        ->seconds(false)
                        ->required(),
                    Forms\Components\TimePicker::make('ends_at')
                        ->seconds(false)
                        ->required(),
                ])
                ->columns(3)
                ->collapsed()
                ->defaultItems(0)
                ->disabled(fn (Forms\Get $get) => (bool) $get('is_closed'))
                ->reorderable(false);
        }

        return $form->schema($schema)->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Day')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        7 => 'Sunday',
                        default => '—',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_closed')
                    ->label('Closed')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('opens_at')
                    ->label('Opens')
                    ->sortable(),
                Tables\Columns\TextColumn::make('closes_at')
                    ->label('Closes')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('day_of_week');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('admin_id', Auth::guard('admin')->id())
            ->when(
                Schema::hasTable('admin_operating_breaks'),
                fn (Builder $query) => $query->with('breaks')
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOperatingHours::route('/'),
            'create' => Pages\CreateOperatingHour::route('/create'),
            'edit' => Pages\EditOperatingHour::route('/{record}/edit'),
        ];
    }
}
