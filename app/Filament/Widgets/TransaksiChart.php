<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class TransaksiChart extends ChartWidget
{
    protected static ?string $heading = 'Total Transaksi per Hari';
    protected static ?int $sort = 1;
    protected static ?string $maxHeight = '300px'; // Atur tinggi grafik

    protected function getData(): array
    {
        // Ambil 5 hari terakhir termasuk hari ini
    $dates = collect();
    for ($i = 13; $i >= 0; $i--) {
        $dates->push(now()->subDays($i)->format('Y-m-d'));
    }

    // Ambil data dari database
    $transaksiData = Transaksi::selectRaw('DATE(created_at) as date, COUNT(*) as total')
        ->whereDate('created_at', '>=', now()->subDays(13)->startOfDay())
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('total', 'date');

    // Buat dataset dengan semua tanggal dan isi default 0 jika tidak ada data
    $data = $dates->map(fn ($date) => $transaksiData[$date] ?? 0);


        return [
            'datasets' => [
            [
                'label' => 'Jumlah Transaksi',
                'data' => $data,
                'backgroundColor' => 'rgb(212, 99, 64)',
            ],
        ],
        'labels' => $dates->map(fn ($date) => date('d M', strtotime($date))),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
