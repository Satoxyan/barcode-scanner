<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'items',  // Menyimpan data barang dalam bentuk JSON
        'total',  // Total harga seluruh barang
    ];

    protected $casts = [
        'items' => 'array', // Konversi otomatis ke array saat diambil dari database
    ];
}