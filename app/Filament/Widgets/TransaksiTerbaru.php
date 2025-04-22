<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Transaksi;
use Filament\Tables\Columns\TextColumn;

class TransaksiTerbaru extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Terbaru';
    
    protected int | string | array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaksi::query()->latest()->limit(4)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR'),

                TextColumn::make('nominal_uang')
                    ->label('Nominal')
                    ->money('IDR'),

                TextColumn::make('kembalian')
                    ->label('Kembalian')
                    ->money('IDR'),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y - H:i'),
            ]);
    }

    public static function getSort(): int
    {
        return 40; 
    }

}
