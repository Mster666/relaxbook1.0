<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TherapistResource\Pages;
use App\Models\Therapist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class TherapistResource extends Resource
{
    protected static ?string $model = Therapist::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        $hasTherapistDetails = Schema::hasColumn('therapists', 'title')
            && Schema::hasColumn('therapists', 'gender')
            && Schema::hasColumn('therapists', 'languages')
            && Schema::hasColumn('therapists', 'certifications');

        return $form
            ->schema([
                Forms\Components\Section::make('Therapist')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\TextInput::make('title')
                            ->visible($hasTherapistDetails)
                            ->maxLength(191)
                            ->placeholder('Therapist'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(191),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(191),
                        Forms\Components\Select::make('gender')
                            ->visible($hasTherapistDetails)
                            ->options([
                                'Male' => 'Male',
                                'Female' => 'Female',
                                'Other' => 'Other',
                            ]),
                        Forms\Components\TagsInput::make('languages')
                            ->visible($hasTherapistDetails)
                            ->suggestions(['English', 'Tagalog', 'Bisaya'])
                            ->placeholder('Add languages'),
                        Forms\Components\Select::make('services')
                            ->label('Certifications (Services)')
                            ->multiple()
                            ->preload()
                            ->relationship(
                                name: 'services',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('services.admin_id', Auth::guard('admin')->id())
                            )
                            ->placeholder('Select certified services')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('bio')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->directory('therapists')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400')
                            ->imageResizeMode('cover')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $adminId = Auth::guard('admin')->id();

        return $query->where('admin_id', $adminId);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('services_list')
                    ->label('Certifications')
                    ->getStateUsing(fn (\App\Models\Therapist $record) => $record->services->pluck('name')->implode(', '))
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->disk('public')
                    ->size(40),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Availability')
                    ->onColor('success')
                    ->offColor('danger'),
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
            'index' => Pages\ListTherapists::route('/'),
            'create' => Pages\CreateTherapist::route('/create'),
            'edit' => Pages\EditTherapist::route('/{record}/edit'),
        ];
    }
}
