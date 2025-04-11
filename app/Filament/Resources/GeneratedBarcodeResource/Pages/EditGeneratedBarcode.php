<?php

namespace App\Filament\Resources\GeneratedBarcodeResource\Pages;

use App\Filament\Resources\GeneratedBarcodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeneratedBarcode extends EditRecord
{
    protected static string $resource = GeneratedBarcodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
