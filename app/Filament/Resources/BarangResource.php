<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;


class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;
    protected static ?string $navigationIcon = 'heroicon-s-archive-box';
    protected static ?string $navigationGroup = 'Manajemen Barang';

    public static function form(Forms\Form $form): Forms\Form {
        return $form
            ->schema([
                Forms\Components\TextInput::make('barcode')
                            ->label('Barcode')
                            ->required()
                            ->live(), // Agar form bisa mendeteksi perubahan langsung
    
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Barang')
                            ->required(),
        
                        Forms\Components\TextInput::make('harga')
                            ->label('Harga Barang')
                            ->required()
                            ->numeric(),
        
                        Forms\Components\TextInput::make('stok')
                            ->label('Stok Barang')
                            ->numeric()
                            ->required()
                            ->default(0),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barcode')->label('Barcode')
                ->searchable()
                ->copyable()
                ->copyMessage('Barcode Copied')
                ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('nama')->label('Nama Barang')
                ->searchable()
                ->sortable(),

                Tables\Columns\TextColumn::make('harga')->label('Harga Barang')->money('IDR'),

                Tables\Columns\TextColumn::make('stok')->label('Stok'),

                Tables\Columns\TextColumn::make('created_at')->label('dibuat')
                ->hidden(true),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Tambah Barang')
                    ->icon('heroicon-m-plus')
                    ->form([
                        Forms\Components\TextInput::make('barcode')
                            ->label('Barcode')
                            ->required()
                            ->live(), // Agar form bisa mendeteksi perubahan langsung
    
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Barang')
                            ->required(),
        
                        Forms\Components\TextInput::make('harga')
                            ->label('Harga Barang')
                            ->required()
                            ->numeric(),
        
                        Forms\Components\TextInput::make('stok')
                            ->label('Stok Barang')
                            ->numeric()
                            ->required()
                            ->default(0),
                        ])
                        ->action(function (array $data) {
                            \App\Models\Barang::create($data);

                            Notification::make()
                                ->success()
                                ->title('Transaksi berhasil disimpan')
                                ->send();
                        })
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->filters([]);
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListBarangs::route('/'),
            
        ];
    }
}