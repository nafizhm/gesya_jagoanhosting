<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisPembayaran extends Model
{
    use HasFactory;

    protected $table = 'jenis_pembayaran';
    protected $primaryKey = 'id_jenis_pembayaran';
}
