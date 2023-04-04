<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    use HasFactory; 
    protected $fillable = ['pesanan_detail','product_id' ,'pesanan_id' ,'jumlah','jumlah_harga'];
}
