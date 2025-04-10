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
            // Simpan ke tabel barang_transaksi
            BarangTransaksi::create([
                'id_transaksi' => $transaksi->id,
                'nama' => $item['nama'] ?? 'TANPA NAMA', 
                'harga' => $item['harga'] ?? 0,          
                'jumlah' => $item['jumlah'] ?? 1,
                'subtotal' => $item['subtotal'] ?? 0,
            ]);

            // Kurangi stok di tabel barang
            if (!empty($item['barcode'])) {
                $barang = Barang::where('barcode', $item['barcode'])->first();
                if ($barang) {
                    $barang->decrement('stok', $item['jumlah']);
                }
            }
        }

        return $transaksi;
    });
}


}
