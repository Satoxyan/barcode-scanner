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

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
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
                ->searchable(),

                Tables\Columns\TextColumn::make('nama')->label('Nama Barang')
                ->searchable()
                ->sortable(),

                Tables\Columns\TextColumn::make('harga')->label('Harga Barang')->money('IDR'),

                Tables\Columns\TextColumn::make('stok')->label('Stok'),
            ])
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