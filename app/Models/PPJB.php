<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PPJB extends Model
{
    use HasFactory;

    protected $table = 'data_ppjb';

    protected $primaryKey = 'id_ppjb';
    public $timestamps = false;

    protected $fillable = [
        'id_customer',
        'id_kavling',
        'tanggal_ppjb',
        'hari_ppjb',
        'no_ppjb',
        'nama_customer',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'warga_negara',
        'pekerjaan',
        'domisili',
        'no_ktp',
        'alamat_ktp',
        'no_telp',
        'email',
        'lokasi_rumah',
        'blok',
        'kode_kavling',
        'tipe_rumah',
        'luas_tanah',
        'luas_bangunan',
        'daya_listrik',
        'harga_jual',
        'nominal_utj',
        'nominal_dp',
        'sisa_pembayaran',
        'nomor_sertifikat',
        'stt_approval',
        'stt_delete',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }

  public function kavling()
    {
        return $this->belongsTo(KavlingPeta::class, 'id_kavling');
    }

}
