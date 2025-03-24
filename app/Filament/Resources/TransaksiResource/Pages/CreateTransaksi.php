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
        // Pastikan 'items' tidak null
        $items = $data['items'] ?? [];

        // Simpan transaksi utama
        $transaksi = Transaksi::create([
            'total' => $data['total'] ?? 0, // Pastikan nilai tidak null
        ]);

        // Simpan detail barang ke tabel barang_transaksi
        foreach ($items as $item) {
            BarangTransaksi::create([
                'id_transaksi' => $transaksi->id,
                'nama' => $item['nama'] ?? 'UNKNOWN', // ✅ Tambahkan default agar tidak null
                'harga' => $item['harga'] ?? 0,
                'jumlah' => $item['jumlah'] ?? 1,
                'subtotal' => $item['subtotal'] ?? 0,
            ]);
        }

        return $transaksi;
    });
}

}
