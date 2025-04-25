<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class TransaksiExport implements FromCollection, WithHeadings
{
    protected $bulan;

    public function __construct($bulan = null)
    {
        $this->bulan = $bulan;
    }

    public function collection()
    {
        return Transaksi::with('barangTransaksi') // Ambil relasi barang
            ->when($this->bulan, function ($query) {
                $query->whereMonth('created_at', (int) $this->bulan);
            })
            ->get()
            ->map(function ($item) {
                $barang = $item->barangTransaksi
                    ->map(fn ($b) => "{$b->nama}×{$b->jumlah}")
                    ->implode(', ');

                return [
                    'id' => $item->id,
                    'barang' => $barang,
                    'total' => $item->total,
                    'nominal_uang' => $item->nominal_uang,
                    'kembalian' => $item->kembalian,
                    'created_at' => Carbon::parse($item->created_at)->format('d-m-Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Barang',
            'Total Harga',
            'Nominal Uang',
            'Kembalian',
            'Tanggal',
        ];
    }
}
