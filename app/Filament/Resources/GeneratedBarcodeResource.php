<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GeneratedBarcodeResource\Pages;
use App\Filament\Resources\GeneratedBarcodeResource\RelationManagers;
use App\Models\GeneratedBarcode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

use Picqer\Barcode\BarcodeGeneratorPNG;


class GeneratedBarcodeResource extends Resource
{
    protected static ?string $model = GeneratedBarcode::class;
    protected static ?string $navigationLabel = 'Buat Barcode';
    protected static ?string $navigationIcon = 'heroicon-s-qr-code';
    protected static ?string $navigationGroup = 'Manajemen Barang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('barcode_image')
                    ->label('Barcode')
                    ->disk('public')
                    ->getStateUsing(function ($record) {
                        $generator = new BarcodeGeneratorPNG();
                        $barcode = $generator->getBarcode($record->kode, $generator::TYPE_CODE_128);

                        $filename = "barcodes/{$record->id}.png";

                        if (!Storage::disk('public')->exists($filename)) {
                            Storage::disk('public')->put($filename, $barcode);
                        }

                        return $filename;
                    })
                    ->url(fn ($record) => Storage::url("barcodes/{$record->id}.png"))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->copyMessage("Barcode Copied")
                    ->copyMessageDuration(1500)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->hidden(true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Tambah Barang')
                    ->icon('heroicon-m-plus')
                    ->form([
                        Forms\Components\TextInput::make('kode')
                            ->label('Input Kode')
                            ->numeric()
                            ->required(),
                    
                        Forms\Components\TextInput::make('nama_barang')
                            ->label('Nama Barang')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        \App\Models\GeneratedBarcode::create($data);

                        Notification::make()
                            ->success()
                            ->title('Transaksi berhasil disimpan')
                            ->send();
                    })
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => Storage::url("barcodes/{$record->id}.png"))
                    ->openUrlInNewTab()
                    ->extraAttributes(function ($record) {
                        return [
                            'download' => "{$record->kode}.png", // Ganti nama file unduhan dengan kode
                        ];
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeneratedBarcodes::route('/'),
        ];
    }
}
