<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedBarcode extends Model
{
    protected $table = 'generated_barcodes';

    protected $fillable = ['kode','nama_barang'];
}
