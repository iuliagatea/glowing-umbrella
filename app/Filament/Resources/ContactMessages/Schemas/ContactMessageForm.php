<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->disabled(),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->disabled(),
                TextInput::make('phone')
                    ->disabled(),
                Textarea::make('message')
                    ->required()
                    ->rows(5)
                    ->disabled()
                    ->columnSpanFull(),
                DateTimePicker::make('read_at')
                    ->label('Read at'),
            ]);
    }
}
