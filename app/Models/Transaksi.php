<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'total',
        'nominal_uang',
        'kembalian',
    ];
    
    
    public function barangTransaksi()
    {
        return $this->hasMany(BarangTransaksi::class, 'id_transaksi');
    }
}