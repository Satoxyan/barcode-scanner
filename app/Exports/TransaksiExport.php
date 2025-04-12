<?php

namespace App\Exports;

use App\Models\AppModelsTransaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\WithHeadings;

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
            ->get();
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

