<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriModel extends Model
{
    //
    use HasFactory;

    protected $table = 'kategori';

    protected $primaryKey = 'kategori_id';

    protected $fillable = ['kategori', 'jenis_kategori', 'stt_fix'];
    public function transaksi()
    {
        return $this->hasMany(Keuangan::class, 'transaksi_kategori', 'kategori_id');
    }

    public $timestamps = false;

}
