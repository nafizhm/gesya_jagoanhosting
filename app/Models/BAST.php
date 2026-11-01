<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BAST extends Model
{
    use HasFactory;

    protected $table = 'bast';

    protected $primaryKey = 'id_bast';
    public $timestamps = false;

    protected $fillable = [
        'id_customer',
        'id_kavling',
        'tanggal_bast',
        'hari_bast',
        'no_bast',
        'nama_customer',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'domisili',
        'no_ktp',
        'alamat_ktp',
        'no_telp',
        'email',
        'npwp',
        'pekerjaan_cust',
        'lokasi_rumah',
        'blok',
        'kode_kavling',
        'tipe_rumah',
        'luas_tanah',
        'luas_bangunan',
        'daya_listrik',
        'nomor_sertifikat',
        'id_rumah_sikumbang',
        'stt_delete',
    ];
    
}
