<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangTransaksi extends Model
{
    use HasFactory;

    protected $table = 'barang_transaksi';

    protected $fillable = ['id_transaksi', 'nama', 'harga', 'jumlah', 'subtotal'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }
}