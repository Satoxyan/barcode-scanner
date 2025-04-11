<?php

namespace App\Filament\Resources\GeneratedBarcodeResource\Pages;

use App\Filament\Resources\GeneratedBarcodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneratedBarcodes extends ListRecords
{
    protected static string $resource = GeneratedBarcodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
