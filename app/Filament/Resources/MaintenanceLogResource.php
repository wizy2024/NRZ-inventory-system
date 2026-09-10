<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceLogResource\Pages;
use App\Models\MaintenanceLog;
use App\Models\Asset;
use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MaintenanceLogResource\Pages\ListMaintenanceLogs;
use App\Filament\Resources\MaintenanceLogResource\Pages\CreateMaintenanceLog;
use App\Filament\Resources\MaintenanceLogResource\Pages\EditMaintenanceLog;
class MaintenanceLogResource extends Resource

{    protected static ?string $model = MaintenanceLog::class;

    public static function canCreate(): bool
    {
        return Gate::allows('manage maintenance');
    }

    public static function canEdit($record): bool
    {
        return Gate::allows('manage maintenance');
    }

    public static function canDelete($record): bool
    {
        return Gate::allows('manage maintenance');
    }

    public static function canDeleteAny(): bool
    {
        return Gate::allows('manage maintenance');
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static string|\UnitEnum|null $navigationGroup = 'Maintenance';

    public static function canViewAny(): bool
    {
        return Gate::allows('view maintenance');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('asset_id')
                    ->label('Asset')
                    ->native(false)
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Asset::query()
                        ->where('status', 'active')
                        ->where('asset_tag', 'like', "%{$search}%")
                        ->orderBy('asset_tag')
                        ->limit(50)
                        ->pluck('asset_tag', 'id')
                        ->all())
                    ->getOptionLabelUsing(fn ($value): ?string => Asset::find($value)?->asset_tag)
                    ->live(onBlur: true)
                    ->default(fn (): ?int => request()->integer('asset_id') ?: null)
                    ->required(),
                Select::make('technician_id')
                    ->label('Technician')
                    ->native(false)
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => User::query()
                        ->where('name', 'like', "%{$search}%")
                        ->orderBy('name')
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->all())
                    ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name)
                    ->live(onBlur: true)
                    ->nullable(),
                TextInput::make('symptom')
                    ->label('Issue summary')
                    ->placeholder('e.g. Device will not power on')
                    ->live(onBlur: true)
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Detailed Description')
                    ->live(onBlur: true)
                    ->rows(3),
                Select::make('status')
                    ->label('Work status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                    ])
                    ->native(false)
                    ->live(onBlur: true)
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('resolved_at')
                    ->live(onBlur: true)
                    ->nullable()
                    ->visible(fn (callable $get) => $get('status') === 'resolved'),
                Textarea::make('resolution_notes')
                    ->live(onBlur: true)
                    ->nullable()
                    ->visible(fn (callable $get) => $get('status') === 'resolved'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.asset_tag')
                    ->label('Asset')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('technician.name')
                    ->label('Technician')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('symptom')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'resolved' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                    ]),
                SelectFilter::make('asset')
                    ->relationship('asset', 'asset_tag'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['asset', 'technician']);
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
            'index' => ListMaintenanceLogs::route('/'),
            'create' => CreateMaintenanceLog::route('/create'),
            'edit' => EditMaintenanceLog::route('/{record}/edit'),
        ];
    }
}
