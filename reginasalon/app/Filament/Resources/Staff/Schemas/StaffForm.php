<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Service;
use App\Models\ServiceCategory;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;

class StaffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // KIRI
                Section::make()
                    ->schema([
                        Select::make('store_id')
                            ->relationship('store', 'name')
                            ->label('Store')
                            ->required(),
                        TextInput::make('name')
                            ->required()
                            ->placeholder('Name'),
                        TextInput::make("email")
                            ->required()
                            ->unique()
                            ->placeholder('Email'),
                        TextInput::make('phone')
                            ->required()
                            ->unique()
                            ->placeholder('Phone Number')
                            ->helperText('Format: 0878xxxxxxxx'),

                    ]),

                // KANAN
                Section::make()
                    ->schema([
                        FileUpload::make('image')
                            ->label('Self-Photo')
                            ->required()
                            ->image()
                            ->circleCropper()
                            ->previewable()
                            ->panelLayout('integrated')
                            ->imagePreviewHeight('300')
                            ->disk('public')
                            ->directory('staff')
                            ->visibility('public')
                            ->hint('Upload your photo here')
                    ]),

                // SERVICE
                CheckboxList::make('services')
                    ->label('Services')
                    ->required()
                    ->relationship('services', 'name')
                    ->helperText('Choose your specialty')
                    ->columns(3)
                    ->options(fn() => Service::query()
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->columnSpanFull()

            ]);
    }
}
