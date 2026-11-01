<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArsipNasabah extends Model
{
    use HasFactory;

    protected $table = 'nasabah_arsip';

    protected $primaryKey = 'id_customer_arsip';

    public $timestamps = false;

    protected $fillable = [
        'id_customer',
        'tgl_terima',
        'nama_lengkap',
        'kode_customer',
        'no_ktp',
        'tempat_lahir',
        'jenis_kelamin',
        'no_wa',
        'alamat',
        'alamat_domisili',
        'pekerjaan',
        'id_marketing',
        'id_status_progres',
        'no_ktp_p',
        'id_lokasi',
        'id_kavling',
        'id_freelance',
        'tgl_lahir',

    ];

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'id_marketing');
    }

    public function freelance()
    {
        return $this->belongsTo(Freelance::class, 'id_freelance');
    }

    public function lokasi()
    {
        return $this->belongsTo(LokasiKavling::class, 'id_lokasi', 'id_lokasi');
    }

    public function kavling()
    {
        return $this->belongsTo(KavlingPeta::class, 'id_kavling', 'id_kavling');
    }

    public function progres()
    {
        return $this->belongsTo(ProgresListPenjualan::class, 'id_status_progres', 'id_status_progres');
    }

    public function lokasiKavling()
    {
        return $this->belongsTo(LokasiKavling::class, 'id_lokasi');
    }

    public function kavlingPeta()
    {
        return $this->belongsTo(KavlingPeta::class, 'id_kavling');
    }


    public function hakAkses()
    {
        return $this->hasMany(HakAkses::class, 'id_menu')->onDelete('cascade');
    }
}
