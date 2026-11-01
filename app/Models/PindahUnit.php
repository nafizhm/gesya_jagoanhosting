<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PindahUnit extends Model
{
    use HasFactory;

    protected $table = 'pindah_rumah';

    protected $primaryKey = 'id_pindah';
    public $timestamps = false;

    protected $fillable = [
        'tgl_pindah',
        'no_kwitansi',
        'id_customer',
        'id_kavling_lama',
        'id_kavling_baru',
        'nama_customer',
        'kode_kavling_lama',
        'kode_kavling_baru',
        'no_ktp',
        'alamat',
        'nama_perumahan',
        'tanggal_utj',
        'nominal_utj',
        'biaya_admin',
        'keterangan_pindah',
        'stt_approval',
        'stt_delete',
        'jumlah_bayar',
        'metode_bayar',
        'nama_bank',
        'no_rekening',
        'atas_nama',
        'lampiran_bukti',
    ];

}
