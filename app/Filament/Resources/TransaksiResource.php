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
                            ->readOnly(),

                        TextInput::make('nama')
                            ->label('Nama Barang')
                            ->dehydrated()
                            ->readOnly(),

                        TextInput::make('harga')
                            ->label('Harga')
                            ->dehydrated()
                            ->readOnly()
                            ->numeric(),

                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->live()
                            ->dehydrated()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $set('subtotal', $state * $get('harga'));
                                $items = $get('items');
                                $total = collect($items)->sum('subtotal');
                                $set('total', $total);
                            }),

                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->readOnly()
                            ->dehydrated()
                            ->numeric(),
                    ])
                    ->columns(5)
                    ->columnSpanFull()
                    ->dehydrated()
                    ->default([])
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $total = collect($state)->sum('subtotal');
                        $set('total', $total);
                    }),

                TextInput::make('total')
                    ->label('Total Harga')
                    ->readOnly()
                    ->numeric()
                    ->live()
                    ->dehydrated()
                    ->reactive()
                    ->default([])
                    ->extraAttributes(['class' => 'text-large'])
                    ->default(fn (callable $get) => collect($get('items') ?? [])->sum('subtotal')),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('id')
                ->label('ID')
                ->sortable()
                ->searchable(),

                Tables\Columns\TextColumn::make('total')
                ->label('Total Harga')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Tanggal')
                ->dateTime('d-m-Y H:i')
                ->sortable(),
        ])
        ->defaultSort('created_at', 'desc');
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
