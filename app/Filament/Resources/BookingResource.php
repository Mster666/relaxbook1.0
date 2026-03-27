<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('therapist_id')
                    ->relationship('therapist', 'name'),
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name'),
                Forms\Components\Select::make('room_id')
                    ->relationship('room', 'name')
                    ->nullable(),
                Forms\Components\DatePicker::make('booking_date')
                    ->required(),
                Forms\Components\TimePicker::make('booking_time')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\Section::make('Assigned Therapists')
                    ->visible(fn (?Booking $record) => filled($record?->id) && Schema::hasTable('booking_service_therapist'))
                    ->schema([
                        Forms\Components\Placeholder::make('service_therapist_assignments')
                            ->label('Therapist per Service')
                            ->content(function (?Booking $record): HtmlString {
                                if (! $record) {
                                    return new HtmlString('—');
                                }

                                $serviceNames = $record->services()->pluck('name', 'services.id')->toArray();
                                if (empty($serviceNames) && $record->service) {
                                    $serviceNames = [$record->service->id => $record->service->name];
                                }

                                $rows = \DB::table('booking_service_therapist')
                                    ->leftJoin('services', 'services.id', '=', 'booking_service_therapist.service_id')
                                    ->leftJoin('therapists', 'therapists.id', '=', 'booking_service_therapist.therapist_id')
                                    ->where('booking_service_therapist.booking_id', $record->id)
                                    ->select([
                                        'booking_service_therapist.service_id',
                                        'services.name as service_name',
                                        'therapists.name as therapist_name',
                                    ])
                                    ->get();

                                $map = [];
                                foreach ($rows as $row) {
                                    $sid = (int) $row->service_id;
                                    $map[$sid] = [
                                        'service' => $row->service_name ?: ($serviceNames[$sid] ?? 'Service'),
                                        'therapist' => $row->therapist_name ?: ($record->therapist?->name ?? 'Unassigned'),
                                    ];
                                }

                                foreach ($serviceNames as $sid => $name) {
                                    $sid = (int) $sid;
                                    if (! isset($map[$sid])) {
                                        $map[$sid] = [
                                            'service' => $name,
                                            'therapist' => $record->therapist?->name ?? 'Unassigned',
                                        ];
                                    }
                                }

                                if (empty($map)) {
                                    return new HtmlString('—');
                                }

                                $items = collect($map)
                                    ->values()
                                    ->map(function (array $pair) {
                                        $service = e($pair['service']);
                                        $therapist = e($pair['therapist']);
                                        return "<li><span style=\"font-weight:600\">{$service}</span> → {$therapist}</li>";
                                    })
                                    ->implode('');

                                return new HtmlString("<ul style=\"margin:0;padding-left:1.25rem\">{$items}</ul>");
                            }),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TagsColumn::make('services_selected')
                    ->label('Services')
                    ->getStateUsing(function (Booking $record): array {
                        $names = $record->relationLoaded('services')
                            ? $record->services->pluck('name')->all()
                            : $record->services()->pluck('name')->all();

                        if (empty($names) && $record->service) {
                            $names = [$record->service->name];
                        }

                        return array_values(array_filter(array_map('strval', $names)));
                    }),
                Tables\Columns\TextColumn::make('assigned_therapists')
                    ->label('Assigned therapist')
                    ->getStateUsing(function (Booking $record): string {
                        $names = [];

                        if (Schema::hasTable('booking_service_therapist')) {
                            $assignments = $record->relationLoaded('bookingServiceTherapists')
                                ? $record->bookingServiceTherapists
                                : $record->bookingServiceTherapists()->with('therapist:id,name')->get();

                            $names = $assignments
                                ->map(fn ($row) => $row->therapist?->name)
                                ->filter()
                                ->unique()
                                ->values()
                                ->all();
                        }

                        if (empty($names) && $record->therapist) {
                            $names = [$record->therapist->name];
                        }

                        return ! empty($names) ? implode(', ', $names) : '—';
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('room.code')
                    ->label('Room code')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('room.name')
                    ->label('Room')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('booking_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('booking_time'),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $adminId = Auth::guard('admin')->id();

        return $query
            ->where('admin_id', $adminId)
            ->with([
                'user:id,name',
                'therapist:id,name',
                'service:id,name',
                'services:id,name',
                'room:id,name,code',
                'bookingServiceTherapists.therapist:id,name',
            ]);
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
