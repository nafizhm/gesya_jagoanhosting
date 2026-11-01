<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembatalan extends Model
{
    use HasFactory;

    protected $table = 'pembatalan';

    protected $primaryKey = 'id_batal';
    public $timestamps = false;

    protected $fillable = [
        'tgl_batal',
        'no_kwitansi',
        'id_customer',
        'id_kavling',
        'nama_customer',
        'kode_kavling',
        'no_ktp',
        'alamat',
        'nama_perumahan',
        'tanggal_utj',
        'nominal_utj',
        'nama_bank',
        'no_rekening',
        'jumlah_bayar',
        'metode_bayar',
        'lampiran_bukti',
        'atas_nama',
        'keterangan_batal',
        'stt_approval',
        'stt_delete',
    ];
    
        public function kavling()
    {
        return $this->belongsTo(\App\Models\KavlingPeta::class, 'id_kavling', 'id_kavling');
    }

}
