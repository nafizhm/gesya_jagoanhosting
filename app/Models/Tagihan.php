<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';
    public $timestamps = false;
    
    protected $fillable = [
        'nasabah',
        'jenis_tagihan',
        'jumlah_tagihan',
        'utama',
        'catatan',
    ];

}
