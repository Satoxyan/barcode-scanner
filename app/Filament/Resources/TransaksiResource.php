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
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Carbon;



class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;
    protected static ?string $navigationIcon = 'heroicon-s-banknotes';
    protected static ?string $navigationGroup = 'Kasir';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('barcodeInput')
                    ->label('Scan Barcode')
                    ->live()
                    ->numeric()
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
                        
                                // Hitung ulang total setelah subtotal diperbarui
                                $items = $get('../items') ?? []; // naik 2 level dari repeater
                                $items[$get('__index')]['subtotal'] = $subtotal; // perbarui subtotal di array
                        
                                $total = collect($items)->sum('subtotal');
                                $set('../../total', $total); // set total di luar repeater
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
                    ->default([])
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
                    
                    ])                    
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

            Tables\Columns\TextColumn::make('nominal_uang')
                ->label('Nominal Uang')
                ->sortable(),

            Tables\Columns\TextColumn::make('kembalian')
                ->label('Kembalian')
                ->sortable(),

        ])
        ->defaultSort('created_at', 'desc')
        ->headerActions([
            Tables\Actions\Action::make('export')
                ->label('Print Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    Forms\Components\Select::make('bulan')
                        ->label('Pilih Bulan')
                        ->options([
                            '1'  => 'Januari',
                            '2'  => 'Februari',
                            '3'  => 'Maret',
                            '4'  => 'April',
                            '5'  => 'Mei',
                            '6'  => 'Juni',
                            '7'  => 'Juli',
                            '8'  => 'Agustus',
                            '9'  => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $bulan = $data['bulan'];

                    $bulanMap = [
                        '1'  => 'Januari',
                        '2'  => 'Februari',
                        '3'  => 'Maret',
                        '4'  => 'April',
                        '5'  => 'Mei',
                        '6'  => 'Juni',
                        '7'  => 'Juli',
                        '8'  => 'Agustus',
                        '9'  => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ];

                    $namaBulan = $bulanMap[$bulan] ?? 'Tanpa Bulan';
                    $filename = 'Transaksi_' . $namaBulan . '.xlsx';

                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\TransaksiExport($bulan),
                        $filename
                    );
                }),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])    
        ->filters([
            SelectFilter::make('bulan')
                ->label('Filter Bulan')
                ->options(
                    collect(range(1, 12))->mapWithKeys(function ($bulan) {
                        return [
                            $bulan => Carbon::create()->month($bulan)->translatedFormat('F'),
                        ];
                    })->toArray()
                )
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['value'], function ($query, $bulan) {
                        return $query->whereMonth('created_at', $bulan);
                    });
                }),
        ])  
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
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
