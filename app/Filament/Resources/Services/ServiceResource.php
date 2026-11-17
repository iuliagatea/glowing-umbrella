<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Resources\Services\Schemas\ServiceForm;
use App\Filament\Resources\Services\Tables\ServicesTable;
use App\Models\Service;
use BackedEnum;
use Filament\Forms\Components\{Select, TextInput, Textarea, Toggle};
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\{TextColumn, ToggleColumn};
use Filament\Tables\Table;
use Filament\Actions\{EditAction, DeleteAction};

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $recordTitleAttribute = 'service';
    
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('service_category_id')
                ->relationship('category', 'name')->required(),
            TextInput::make('name')->required()->maxLength(190),
            Textarea::make('description')->rows(3),
            TextInput::make('price')->numeric()->prefix('RON')->required(),
            TextInput::make('price_note')->label('Price note (e.g. "from")')->maxLength(40),
            TextInput::make('sort_order')->numeric()->default(0),
            Toggle::make('is_active')->label('Visible on website')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('price')->money('RON')->sortable(),
                ToggleColumn::make('is_active')->label('Visible'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('sort_order');
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
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
