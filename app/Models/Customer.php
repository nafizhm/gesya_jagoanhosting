<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ArsipNasabah;
use DateTime;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'nasabah';

    protected $primaryKey = 'id_customer';

    public function pindahUnitKeluar()
    {
        return $this->hasMany(PindahUnit::class, 'id_customer', 'id_customer');
    }

    protected $fillable = [
        'id_reg',
        'tgl_terima',
        'nama_lengkap',
        'kode_customer',
        'no_ktp',
        'tempat_lahir',
        'jenis_kelamin',
        'no_wa',
        'email',
        'npwp',
        'alamat',
        'alamat_domisili',
        'pekerjaan',
        'warga_negara',
        'hrg_rumah',
        'id_marketing',
        'id_status_progres',
        'ket_cashback',
        'jenis_pembelian',
        'no_ktp_p',
        'id_lokasi',
        'id_kavling',
        'tgl_lahir',
        'id_freelance',
        'no_kk',
        'penghasilan',
        'id_bank',
        'id_admin_pemberkasan',
        'jenis_pembelian',
        'jumlah_bulan',
        'pembayaran_booking',
        'tgl_batas_booking',
        'keterangan_booking',
        'inhouse_perbulan',
        'inhouse_tenor',
        'inhouse_jatuh_tempo',
    ];

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'nasabah', 'id_customer');
    }

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'id_marketing');
    }

    public function bank()
    {
        return $this->belongsTo(BankModel::class, 'id_bank');
    }

    public function freelance()
    {
        return $this->belongsTo(Freelance::class, 'id_freelance', 'id_freelance');
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

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_customer');
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'id_customer');
    }

    public function wawancara()
    {
        return $this->hasOne(Wawancara::class, 'id_customer', 'id_customer');
    }

    public function files()
    {
        return $this->hasMany(UploadFile::class, 'nasabah', 'id_customer');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($customer) {
            $tglLahir = $customer->tgl_lahir;

            if (empty($tglLahir) || $tglLahir == '0000-00-00' || $tglLahir == '0000-00-00 00:00:00') {
                $tglLahir = null;
            } else {
                try {
                    new DateTime($tglLahir);
                } catch (\Exception $e) {
                    $tglLahir = null;
                }
            }

            ArsipNasabah::create([
                'id_customer'       => $customer->id_customer,
                'tgl_terima'        => $customer->tgl_terima,
                'nama_lengkap'      => $customer->nama_lengkap,
                'kode_customer'     => $customer->kode_customer,
                'no_ktp'            => $customer->no_ktp,
                'tempat_lahir'      => $customer->tempat_lahir,
                'jenis_kelamin'     => $customer->jenis_kelamin,
                'no_wa'             => $customer->no_wa,
                'alamat'            => $customer->alamat,
                'alamat_domisili'   => $customer->alamat_domisili,
                'pekerjaan'         => $customer->pekerjaan,
                'id_marketing'      => $customer->id_marketing,
                'id_status_progres' => $customer->id_status_progres,
                'no_ktp_p'          => $customer->no_ktp_p,
                'id_lokasi'         => $customer->id_lokasi,
                'id_kavling'        => $customer->id_kavling,
                'id_freelance'      => $customer->id_freelance,
                'tgl_lahir'         => $tglLahir,
            ]);
        });
    }

    public $timestamps = false;
}
