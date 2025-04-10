<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;

class EditTransaksi extends EditRecord
{
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->schema([
                        Card::make([
                            Repeater::make('barangTransaksi')
                                ->relationship('barangTransaksi')
                                ->label('Daftar Barang')
                                ->schema([
                                    TextInput::make('nama')
                                        ->label('Nama Barang')
                                        ->readOnly()
                                        ->columnSpan(2),

                                    TextInput::make('harga')
                                        ->label('Harga')
                                        ->readOnly(),

                                    TextInput::make('jumlah')
                                        ->label('Jumlah')
                                        ->numeric()
                                        ->required()
                                        ->reactive()
                                        ->readOnly()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) =>
                                            $set('subtotal', $get('harga') * $state)
                                        ),

                                    TextInput::make('subtotal')
                                        ->label('Subtotal')
                                        ->readOnly(),
                                ])
                                ->columns([
                                    'sm' => 5,
                                    'md' => 5,
                                ])
                                ->defaultItems(0)
                                ->disableItemCreation()
                                ->disableItemDeletion(),
                        ]),

                        Grid::make()
                            ->schema([
                                TextInput::make('total_harga')
                                    ->label('Total Harga')
                                    ->readOnly()
                                    ->reactive()
                                    ->afterStateHydrated(function ($state, callable $set, $get) {
                                        $total = collect($get('barangTransaksi'))->sum('subtotal');
                                        $set('total_harga', $total);
                                    }),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }
}
