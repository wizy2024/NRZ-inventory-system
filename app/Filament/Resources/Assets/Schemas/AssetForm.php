<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('asset_tag')
                    ->label('Asset tag')
                    ->placeholder('e.g. NRZ-IT-0001')
                    ->required()
                    ->maxLength(255),
                TextInput::make('serial_number')
                    ->label('Serial number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('mac_address')
                    ->label('MAC address')
                    ->placeholder('e.g. 00:1A:2B:3C:4D:5E')
                    ->maxLength(17)
                    ->default(null),
                Select::make('type')
                    ->options([
                        'Desktop' => 'Desktop',
                        'Laptop' => 'Laptop',
                        'Server' => 'Server',
                        'Printer' => 'Printer',
                        'Network Device' => 'Network Device',
                        'Other' => 'Other',
                    ])
                    ->native(false)
                    ->searchable()
                    ->required(),
                TextInput::make('brand')
                    ->required()
                    ->maxLength(255),
                KeyValue::make('specs')
                    ->label('Specifications')
                    ->keyLabel('Specification')
                    ->valueLabel('Details')
                    ->addActionLabel('Add specification')
                    ->columnSpanFull(),
                DatePicker::make('purchase_date')
                    ->label('Purchase date')
                    ->native(false),
                DatePicker::make('warranty_expiry')
                    ->label('Warranty expiry')
                    ->native(false),
                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('location_id')
                    ->label('Location / station')
                    ->relationship('location', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('assigned_to_user_id')
                    ->label('Assigned to')
                    ->relationship('assignedTo', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->nullable(),
                DateTimePicker::make('assigned_at')
                    ->label('Assigned on')
                    ->native(false)
                    ->nullable(),
                Textarea::make('assignment_notes')
                    ->label('Assignment notes')
                    ->rows(2)
                    ->nullable()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'decommissioned' => 'Decommissioned',
                    ])
                    ->native(false)
                    ->required()
                    ->default('active'),
                Textarea::make('condemnation_reason')
                    ->label('Condemnation reason')
                    ->rows(3)
                    ->default(null)
                    ->columnSpanFull(),
                DatePicker::make('condemned_at'),
            ]);
    }
}
