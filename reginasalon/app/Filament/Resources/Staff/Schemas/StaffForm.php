<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Service;
use App\Models\ServiceCategory;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;

class StaffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make()
                    ->schema([
                        Select::make('store_id')->relationship('store', 'name')->label('Store')->required(),
                        TextInput::make('name')->required(),
                        TextInput::make("email")->required()->unique(),
                        TextInput::make('phone')
                            ->required()
                            ->unique()
                            ->helperText('Format: 0878xxxxxxxx')

                    ]),
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


                CheckboxList::make('services')
                    ->label('Services')
                    ->required()
                    ->relationship('services', 'name')
                    ->helperText('Pilih service yang bisa dilakukan staff ini.')
                    ->columns(3)
                    ->options(fn() => Service::query()
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->columnSpanFull()

            ]);
    }
}
