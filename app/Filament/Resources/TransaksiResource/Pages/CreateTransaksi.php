<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Transaksi;
use App\Models\Barang;
use Illuminate\Database\Eloquent\Model;
use App\Models\BarangTransaksi;
use Illuminate\Support\Facades\DB;

class CreateTransaksi extends CreateRecord
{
    protected static string $resource = TransaksiResource::class;

    protected function handleRecordCreation(array $data): Model
{
    return DB::transaction(function () use ($data) {
        $items = $data['items'] ?? [];

        $transaksi = Transaksi::create([
            'total' => $data['total'] ?? 0,
        ]);

        foreach ($items as $item) {
            BarangTransaksi::create([
                'id_transaksi' => $transaksi->id,
                'nama' => $item['nama'] ?? 'TANPA NAMA',  // ✅ Pastikan mengambil dari $item
                'harga' => $item['harga'] ?? 0,          // ✅ Pastikan mengambil dari $item
                'jumlah' => $item['jumlah'] ?? 1,
                'subtotal' => $item['subtotal'] ?? 0,
            ]);
        }

        return $transaksi;
    });
}

}
