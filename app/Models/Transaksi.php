<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'tanggal_transaksi',
        'total_harga',
    ];

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');

}
}