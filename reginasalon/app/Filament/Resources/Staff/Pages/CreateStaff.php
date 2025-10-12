<?php

namespace App\Filament\Resources\Staff\Pages;

use App\Filament\Resources\Staff\StaffResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    // Redirect ke index staff
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
