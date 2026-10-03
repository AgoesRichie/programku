<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_nota', 
        'total_harga', 
        'kode_promo', // Tambahan baru
        'diskon',     // Tambahan baru
        'uang_bayar', 
        'uang_kembali'
    ];
}