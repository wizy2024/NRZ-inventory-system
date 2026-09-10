<?php

namespace App\Filament\Resources\Audits;

use App\Filament\Resources\Audits\Pages\CreateAudit;
use App\Filament\Resources\Audits\Pages\EditAudit;
use App\Filament\Resources\Audits\Pages\ListAudits;
use App\Filament\Resources\Audits\Schemas\AuditForm;
use App\Filament\Resources\Audits\Tables\AuditsTable;
use App\Models\Audit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Audit checks';

    public static function canViewAny(): bool
    {
        return Gate::allows('manage audits');
    }

    public static function canCreate(): bool
    {
        return Gate::allows('manage audits');
    }

    public static function canEdit($record): bool
    {
        return Gate::allows('manage audits');
    }

    public static function canDelete($record): bool
    {
        return Gate::allows('manage audits');
    }

    public static function canDeleteAny(): bool
    {
        return Gate::allows('manage audits');
    }

    public static function form(Schema $schema): Schema
    {
        return AuditForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['asset', 'auditor']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAudits::route('/'),
            'create' => CreateAudit::route('/create'),
            'edit' => EditAudit::route('/{record}/edit'),
        ];
    }
}
