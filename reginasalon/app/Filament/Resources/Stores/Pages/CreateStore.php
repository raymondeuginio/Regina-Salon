<?php

namespace App\Filament\Resources\Stores\Pages;

use App\Filament\Resources\Stores\StoreResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStore extends CreateRecord
{
    protected static string $resource = StoreResource::class;

    // Redirect ke index service category
    protected function getRedirectUrl(): string
    {
        return $this->getResource::getUrl('index');
    }
}
