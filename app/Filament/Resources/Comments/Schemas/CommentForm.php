<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('commentable_type')
                    ->required(),
                TextInput::make('commentable_id')
                    ->required()
                    ->numeric(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                    ->default('pending')
                    ->required(),
                TextInput::make('moderated_by')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('moderated_at'),
                Textarea::make('moderation_note')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('ip_address')
                    ->default(null),
                Toggle::make('is_edited')
                    ->required(),
            ]);
    }
}
