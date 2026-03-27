<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $hasRoomDetails = Schema::hasColumn('rooms', 'code')
            && Schema::hasColumn('rooms', 'capacity_max')
            && Schema::hasColumn('rooms', 'status')
            && Schema::hasColumn('rooms', 'amenities')
            && Schema::hasColumn('rooms', 'image');

        $hasRoomType = Schema::hasColumn('rooms', 'room_type');

        return $form
            ->schema([
                Forms\Components\Section::make('Room')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Enabled')
                            ->helperText('Turn off to hide this room from booking.')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Capacity & Type')
                    ->visible($hasRoomDetails)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->maxLength(32)
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? strtoupper(trim((string) $state)) : null)
                            ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->where('admin_id', Auth::guard('admin')->id()))
                            ->helperText('Unique per company/admin. Example: A101.')
                            ->nullable(),
                        Forms\Components\Select::make('room_type')
                            ->visible($hasRoomType)
                            ->options([
                                'private' => 'Private (1 person)',
                                'couple' => 'Couple (2 persons)',
                                'family' => 'Family (3–4 persons)',
                                'group' => 'Group (5+ persons)',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (! $state) {
                                    return;
                                }
                                match ($state) {
                                    'private' => $set('capacity_max', 1),
                                    'couple' => $set('capacity_max', 2),
                                    'family' => $set('capacity_max', 4),
                                    'group' => $set('capacity_max', 6),
                                    default => null,
                                };
                            }),
                        Forms\Components\TextInput::make('capacity_max')
                            ->label('Max occupants')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(99)
                            ->required()
                            ->default(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Availability')
                    ->visible($hasRoomDetails)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'available' => 'Not Occupied',
                                'occupied' => 'Occupied',
                                'maintenance' => 'Under Maintenance',
                            ])
                            ->default('available')
                            ->required(),
                        Forms\Components\Select::make('amenities')
                            ->multiple()
                            ->options([
                                'wifi' => 'WiFi',
                                'ac' => 'AC',
                                'coffee' => 'Coffee',
                                'tv' => 'TV',
                            ])
                            ->default([]),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('rooms')
                            ->imagePreviewHeight('160')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $columns = [
            Tables\Columns\TextInputColumn::make('name')
                ->rules(['required', 'max:191'])
                ->searchable()
                ->sortable(),
        ];

        if (Schema::hasColumn('rooms', 'code')) {
            $columns[] = Tables\Columns\TextInputColumn::make('code')
                ->label('Code')
                ->rules(function (Room $record): array {
                    return [
                        (new Unique('rooms', 'code'))
                            ->where('admin_id', $record->admin_id)
                            ->ignore($record->id),
                    ];
                })
                ->sortable()
                ->searchable();
        }

        if (Schema::hasColumn('rooms', 'room_type')) {
            $columns[] = Tables\Columns\TextColumn::make('room_type')
                ->label('Type')
                ->badge()
                ->formatStateUsing(fn (?string $state): string => match ($state) {
                    'private' => 'Private',
                    'couple' => 'Couple',
                    'family' => 'Family',
                    'group' => 'Group',
                    default => '—',
                })
                ->toggleable(isToggledHiddenByDefault: true);
        }

        if (Schema::hasColumn('rooms', 'status')) {
            $columns[] = Tables\Columns\SelectColumn::make('status')
                ->options([
                    'available' => 'Not Occupied',
                    'occupied' => 'Occupied',
                    'maintenance' => 'Under Maintenance',
                ])
                ->rules(['required'])
                ->selectablePlaceholder(false)
                ->sortable();
        }

        if (Schema::hasColumn('rooms', 'capacity_max')) {
            $columns[] = Tables\Columns\TextInputColumn::make('capacity_max')
                ->label('Max occupants')
                ->rules(['required', 'integer', 'min:1', 'max:99'])
                ->sortable();
        }

        if (Schema::hasColumn('rooms', 'amenities')) {
            $columns[] = Tables\Columns\TagsColumn::make('amenities')
                ->label('Amenities')
                ->getStateUsing(function (Room $record): array {
                    $items = is_array($record->amenities) ? $record->amenities : [];

                    return collect($items)
                        ->map(fn (string $v) => match ($v) {
                            'wifi' => 'WiFi',
                            'ac' => 'AC',
                            'coffee' => 'Coffee',
                            'tv' => 'TV',
                            default => strtoupper($v),
                        })
                        ->values()
                        ->all();
                });
        }

        $columns[] = Tables\Columns\ToggleColumn::make('is_active')
            ->label('Enabled')
            ->onColor('success')
            ->offColor('danger');

        return $table
            ->columns($columns)
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Not Occupied',
                        'occupied' => 'Occupied',
                        'maintenance' => 'Under Maintenance',
                    ])
                    ->visible(Schema::hasColumn('rooms', 'status')),
                Tables\Filters\Filter::make('capacity')
                    ->form([
                        Forms\Components\TextInput::make('min')
                            ->label('Min occupants')
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('max')
                            ->label('Max occupants')
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->query(function ($query, array $data) {
                        if (! Schema::hasColumn('rooms', 'capacity_max')) {
                            return $query;
                        }

                        return $query
                            ->when($data['min'] ?? null, fn ($q, $min) => $q->where('capacity_max', '>=', (int) $min))
                            ->when($data['max'] ?? null, fn ($q, $max) => $q->where('capacity_max', '<=', (int) $max));
                    })
                    ->visible(Schema::hasColumn('rooms', 'capacity_max')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $adminId = Auth::guard('admin')->id();

        return $query->where('admin_id', $adminId);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
