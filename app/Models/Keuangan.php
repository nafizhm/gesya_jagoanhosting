<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    //
    use HasFactory;

    protected $table = 'transaksi';

    protected $primaryKey = 'transaksi_id';

    protected $fillable = [
    'transaksi_tanggal',
    'transaksi_jenis',
    'transaksi_kategori',
    'transaksi_keterangan',
    'transaksi_nominal',
    'transaksi_bank',
    'transaksi_barang',
    'id_tbl_pembayaran',
    'no_kwitansi',
    'id_kavling',

];
public function kategori()
{
    return $this->belongsTo(KategoriModel::class, 'transaksi_kategori', 'kategori_id');
}

    public $timestamps = false;

}
