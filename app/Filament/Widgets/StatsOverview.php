<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transaksi;
use App\Models\Barang;

class StatsOverview extends BaseWidget
{
    protected static ?int $maxColumns = 2;
    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Transaksi', Transaksi::count())
                ->description('Jumlah seluruh transaksi')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success')
                ->extraAttributes([
                    'class' => 'text-base md:text-lg', // Ukuran judul & angka
                ]),

            Stat::make('Total Barang', Barang::count())
                ->description('Jumlah semua barang')
                ->descriptionIcon('heroicon-o-cube')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'text-base md:text-lg',
                ]),
        ];
    }

    protected function getColumns(): int
    {
        return 1; // Supaya bertumpuk
    }
}