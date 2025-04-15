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
        return Transaksi::when($this->bulan, function ($query) {
                $query->whereMonth('created_at', (int) $this->bulan);
            })
            ->select('id', 'total', 'nominal_uang', 'kembalian', 'created_at')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
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
            'Total Harga',
            'Nominal Uang',
            'Kembalian',
            'Tanggal',
        ];
    }
}
