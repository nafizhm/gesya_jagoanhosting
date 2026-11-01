<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = false;
    protected $fillable = [
        'tanggal',
        'id_customer',
        'no_kwitansi',
        'id_lokasi',
        'jumlah',
        'keterangan',
        'lampiran',
        'id_jenis_pembayaran',
        'metode_bayar',
        'nama_notaris',
        'stt_delete_dp',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
    }

    public function jenisPembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class, 'id_jenis_pembayaran', 'id_jenis_pembayaran');
    }
}
