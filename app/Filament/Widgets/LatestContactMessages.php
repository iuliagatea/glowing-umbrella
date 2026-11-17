<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestContactMessages extends TableWidget
{
    protected static ?string $heading = 'Latest Contact Messages';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ContactMessage::query())
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('message')
                    ->limit(50),
                IconColumn::make('read_at')
                    ->label('Read')
                    ->boolean()
                    ->getStateUsing(fn (ContactMessage $record) => $record->read_at !== null),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->since(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
