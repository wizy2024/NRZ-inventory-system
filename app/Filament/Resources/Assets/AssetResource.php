<?php

namespace App\Filament\Resources\Assets;

use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Filament\Resources\Assets\Schemas\AssetForm;
use App\Filament\Resources\Assets\Tables\AssetsTable;
use App\Models\Asset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AssetForm::configure($schema);
    }

    public static function canViewAny(): bool
    {
        return Gate::allows('view assets');
    }

    public static function canCreate(): bool
    {
        return Gate::allows('create assets');
    }

    public static function canEdit($record): bool
    {
        return Gate::allows('edit assets');
    }

    public static function canDelete($record): bool
    {
        return Gate::allows('delete assets');
    }

    public static function canDeleteAny(): bool
    {
        return Gate::allows('delete assets');
    }

    public static function table(Table $table): Table
    {
        return AssetsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['department', 'location', 'assignedTo']);
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
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }
}
