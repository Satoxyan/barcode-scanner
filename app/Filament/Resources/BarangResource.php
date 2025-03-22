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
                    ->unique()
                    ->live(), // Agar form bisa mendeteksi perubahan langsung

                Forms\Components\TextInput::make('nama')
                    ->label('Nama Barang')
                    ->required(),

                Forms\Components\TextInput::make('harga')
                    ->label('Harga Barang')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barcode')->label('Barcode'),
                Tables\Columns\TextColumn::make('nama')->label('Nama Barang'),
                Tables\Columns\TextColumn::make('harga')->label('Harga Barang')->money('IDR'),
            ])
            ->filters([]);
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}