<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Barang;
use App\Models\TransaksiDetail;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Kasir';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('barcodeInput')
                    ->label('Scan Barcode')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $barang = Barang::where('barcode', $state)->first();
                        if ($barang) {
                            $items = $get('items') ?? [];
                            $found = false;

                            foreach ($items as &$item) {
                                if ($item['barcode'] === $state) {
                                    $item['jumlah'] += 1;
                                    $item['subtotal'] = $item['harga'] * $item['jumlah'];
                                    $found = true;
                                    break;
                                }
                            }

                            if (!$found) {
                                $items[] = [
                                    'barcode' => $state,
                                    'nama' => $barang->nama,
                                    'harga' => $barang->harga,
                                    'jumlah' => 1,
                                    'subtotal' => $barang->harga,
                                ];
                            }

                            $set('items', $items);
                            $set('barcodeInput', ''); // Hapus input barcode setelah scan

                            // Update total after items are updated
                            $total = collect($items)->sum('subtotal');
                            $set('total', $total);
                        }
                    }),

                Repeater::make('items')
                    ->label('Daftar Barang')
                    ->schema([
                        TextInput::make('barcode')
                            ->label('Barcode')
                            ->dehydrated()
                            ->hidden(fn ($get) => empty($get('nama')))
                            ->disabled(),

                        TextInput::make('nama')
                            ->label('Nama Barang')
                    ->dehydrated()
                            ->hidden(fn ($get) => empty($get('nama')))
                            ->disabled(),

                        TextInput::make('harga')
                            ->label('Harga')
                    ->dehydrated()
                            ->hidden(fn ($get) => empty($get('nama')))
                            ->disabled()
                            ->numeric(),

                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->required()
                            ->hidden(fn ($get) => empty($get('nama')))
                            ->numeric()
                            ->live()
                            ->disabled()
                    ->dehydrated()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $set('subtotal', $state * $get('harga'));
                                $items = $get('items');
                                $total = collect($items)->sum('subtotal');
                                $set('total', $total);
                            }),

                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->disabled()
                    ->dehydrated()
                            ->hidden(fn ($get) => empty($get('nama')))
                            ->numeric(),
                    ])
                    ->columns(5)
                    ->columnSpanFull()
                    ->dehydrated()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $total = collect($state)->sum('subtotal');
                        $set('total', $total);
                    }),

                TextInput::make('total')
                    ->label('Total Harga')
                    ->disabled()
                    ->numeric()
                    ->live()
                    ->dehydrated()
                    ->reactive()
                    ->default([])
                    ->extraAttributes(['class' => 'text-large'])
                    ->default(fn (callable $get) => collect($get('items') ?? [])->sum('subtotal')),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksis::route('/'),
            'create' => Pages\CreateTransaksi::route('/create'),
            'edit' => Pages\EditTransaksi::route('/{record}/edit'),
        ];
    }
}
