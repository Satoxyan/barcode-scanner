<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistoriTransaksi extends Model
{
    use HasFactory;

    protected $table = 'histori_transaksi';

    protected $fillable = ['items', 'total'];

    protected $casts = [
        'items' => 'array', // Agar JSON otomatis dikonversi menjadi array
    ];
}
