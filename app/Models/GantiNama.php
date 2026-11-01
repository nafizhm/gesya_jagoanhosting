<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GantiNama extends Model
{
    protected $table = 'ganti_nama';
    public $timestamps = false;
    protected $fillable = [
        'tgl_ganti',
        'id_customer_lama',
        'id_customer_baru',
        'nama_customer_lama',
        'id_kavling',
        'kode_kavling',
        'biaya_ganti_nama',
        'keterangan_ganti',
        'stt_approval',
        'stt_delete',
    ];
   
    public function arsipCustomerLama()
    {
        return $this->belongsTo(ArsipNasabah::class, 'id_customer_lama', 'id_customer');
    }

    public function customerBaru()
    {
        return $this->belongsTo(Customer::class, 'id_customer_baru');
    }
}
