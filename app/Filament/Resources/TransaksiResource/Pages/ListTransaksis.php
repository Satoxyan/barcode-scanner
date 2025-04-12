<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use App\Models\Barang;
use App\Models\BarangTransaksi;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiDetail;
use App\Models\HistoriTransaksi;
use Filament\Notifications\Notification;


class ListTransaksis extends ListRecords
{
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Buat Transaksi')
            ->icon('heroicon-m-plus')
            ->form(self::getFormSchema()) // ← gunakan schema form yang sama
            ->modalHeading('Transaksi Baru')
            ->modalSubmitActionLabel('Simpan')
            ->slideOver()
            ->action(function (array $data) {
                // Logika simpan bisa disalin dari `CreateTransaksi::handleRecordCreation`
                \DB::transaction(function () use ($data) {
                    $items = $data['items'] ?? [];

                    $transaksi = \App\Models\Transaksi::create([
                        'total' => $data['total'] ?? 0,
                        'nominal_uang' => $data['nominal_uang'] ?? 0,
                        'kembalian' => $data['kembalian'] ?? 0,
                    ]);

                    foreach ($items as $item) {
                        \App\Models\BarangTransaksi::create([
                            'id_transaksi' => $transaksi->id,
                            'nama' => $item['nama'] ?? 'TANPA NAMA',
                            'harga' => $item['harga'] ?? 0,
                            'jumlah' => $item['jumlah'] ?? 1,
                            'subtotal' => $item['subtotal'] ?? 0,
                        ]);

                        if (!empty($item['barcode'])) {
                            $barang = \App\Models\Barang::where('barcode', $item['barcode'])->first();
                            if ($barang) {
                                $barang->decrement('stok', $item['jumlah']);
                            }
                        }
                    }
                });

                Notification::make()
                    ->success()
                    ->title('Transaksi berhasil disimpan')
                    ->send();
            }),
        ];     
    }

    public function getFormSchema(): array
{
    return [
        TextInput::make('barcodeInput')
            ->label('Scan Barcode')
            ->live()
            ->numeric()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $barang = \App\Models\Barang::where('barcode', $state)->first();
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
                    $set('barcodeInput', '');
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
                    ->numeric()
                    ->live()
                    ->reactive()
                    ->dehydrated()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $harga = $get('harga') ?? 0;
                        $subtotal = $state * $harga;
                        $set('subtotal', $subtotal);

                        $items = $get('../items') ?? [];
                        $items[$get('__index')]['subtotal'] = $subtotal;

                        $total = collect($items)->sum('subtotal');
                        $set('../../total', $total);
                    }),

                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->readOnly()
                    ->dehydrated()
                    ->reactive()
                    ->numeric(),
            ])
            ->columns(5)
            ->columnSpanFull()
            ->dehydrated()
            ->required()
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
            ->extraAttributes(['class' => 'text-large'])
            ->default(fn (callable $get) => collect($get('items') ?? [])->sum('subtotal')),

        Grid::make(2)->schema([
            TextInput::make('nominal_uang')
                ->label('Nominal Uang')
                ->numeric()
                ->required()
                ->dehydrated()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $total = (int) $get('total');
                    $set('kembalian', max((int) $state - $total, 0));
                }),

            TextInput::make('kembalian')
                ->label('Kembalian')
                ->readOnly()
                ->dehydrated()
                ->numeric()
                ->reactive(),
        ]),
    ];
}
}
