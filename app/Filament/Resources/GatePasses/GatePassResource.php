<?php

namespace App\Filament\Resources\GatePasses;

use App\Filament\Resources\GatePasses\Pages\CreateGatePass;
use App\Filament\Resources\GatePasses\Pages\EditGatePass;
use App\Filament\Resources\GatePasses\Pages\ListGatePasses;
use App\Filament\Resources\GatePasses\Schemas\GatePassForm;
use App\Filament\Resources\GatePasses\Tables\GatePassesTable;
use App\Models\GatePass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class GatePassResource extends Resource
{
    protected static ?string $model = GatePass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Gate passes';

    public static function canViewAny(): bool
    {
        return Gate::allows('manage gate passes');
    }

    public static function canCreate(): bool
    {
        return Gate::allows('manage gate passes');
    }

    public static function canEdit($record): bool
    {
        return Gate::allows('manage gate passes');
    }

    public static function canDelete($record): bool
    {
        return Gate::allows('manage gate passes');
    }

    public static function canDeleteAny(): bool
    {
        return Gate::allows('manage gate passes');
    }

    public static function form(Schema $schema): Schema
    {
        return GatePassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GatePassesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['asset', 'issuer']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGatePasses::route('/'),
            'create' => CreateGatePass::route('/create'),
            'edit' => EditGatePass::route('/{record}/edit'),
        ];
    }
}
