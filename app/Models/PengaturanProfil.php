<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengaturanProfil extends Model
{
    use HasFactory;

    protected $table = 'konfigurasi';

    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'email',
        'telp',
        'hape',
        'fax',
        'npwp_perusahaan',
        'nama_bank',
        'no_rekening',
        'nama_pemilik_rek',
    ];

    public $timestamps = false;

}
