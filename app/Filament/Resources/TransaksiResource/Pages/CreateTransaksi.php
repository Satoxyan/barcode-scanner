<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Transaksi;
use App\Models\Barang;

class CreateTransaksi extends CreateRecord
{
    protected static string $resource = TransaksiResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        Transaksi::create([
            'items' => json_encode($data['items']), // Simpan sebagai JSON
            'total' => $data['total'],
        ]);
    }
}
