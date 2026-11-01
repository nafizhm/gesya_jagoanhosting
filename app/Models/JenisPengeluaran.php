<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'jenis_pengeluaran';
    protected $primaryKey = 'id_jenis_pembayaran';
}
