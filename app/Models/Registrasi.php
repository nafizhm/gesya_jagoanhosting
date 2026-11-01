<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registrasi extends Model
{
    //
    use HasFactory;

    protected $table = 'registrasi';

    protected $primaryKey = 'id_reg';

    protected $fillable = [
        'no_registrasi',
        'tgl_registrasi',
        'nama_lengkap',
        'nik',
        'kartu_keluarga',
        'no_telp_customer',
        'email',
        'alamat',
        'jenis_kelamin',
        'tempat_lahir',
        'tgl_lahir',
        'npwp',
        'pekerjaan',
        'status',
        'nama_p',
        'nik_p',
        'no_bpjs_kes',
        'nama_saudara',
        'no_telp_saudara',
        'foto_ktp',
        'foto_npwp',
        'foto_kk',
        'foto_bpjs',
        'foto_pemohon',
        'foto_ktp_p',
        'booking_fee',
        'file_bukti',
        'id_marketing',
        'id_freelance',
        'id_lokasi',
        'id_kavling',
        'id_bank',
        'hrg_jual',
        'jenis_perumahan',
        'jenis_pembelian',
        'jumlah_bulan_x',
        'jumlah_pembayaran',
        'cicil_perbulan',
        'tenor_cicilan',
        'jatuh_tempo',
        'stt_reg',
    ];

    public $timestamps = false;


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
}
