<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KavlingPeta extends Model
{
    use HasFactory;

    protected $table = 'kavling_peta';
    protected $primaryKey = 'id_kavling';

    protected $fillable = [
        'id_lokasi',
        'cluster',
        'blok',
        'no',
        'id_perusahaan',
        'kode_kavling',
        'panjang_kanan',
        'panjang_kiri',
        'lebar_depan',
        'lebar_belakang',
        'luas_tanah',
        'tipe_bangunan',
        'daya_listrik',
        'luas_bangunan',
        'hrg_meter',
        'hrg_jual',
        'biaya_lain',
        'id_rumah_sikumbang',
        'no_sertifikat',
        'jenis_map',
        'map',
        'matrik',
        'status',
        'keterangan',
        'atas_nama_surat',
        'id_customer',
        'tgl_jatuh_tempo',
        'tgl_jatuh_tempo',
        'stt_cicilan',
    ];

    public function lokasi()
    {
        return $this->belongsTo(LokasiKavling::class, 'id_lokasi', 'id_lokasi');
    }
    public function customer()
    {
        return $this->hasOne(Customer::class, 'id_kavling', 'id_kavling');
    }
    public function progres()
    {
        return $this->belongsTo(\App\Models\ProgresListPenjualan::class, 'id_status_progres');
    }
    public function unitReady()
    {
        return $this->belongsTo(ProgresUnitReady::class, 'status_ready');
    }
    public function perusahaan() {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }
    public $timestamps = false;
}
