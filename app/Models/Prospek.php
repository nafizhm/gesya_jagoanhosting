<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospek extends Model
{
    //
    use HasFactory;

    protected $table = 'nasabah_prospek';

    protected $primaryKey = 'id_prospek';

    protected $fillable = [
        'tgl_terima',
        'nama_lengkap',
        'no_wa',
        'usia',
        'pekerjaan',
        'penghasilan',
        'sumber_informasi',
        'rangking',
        'id_marketing',
        'id_freelance',
        'keterangan_belum',
        'no_ktp',
        'no_telp',
        'email',
        'stt_delete',

    ];

    public $timestamps = false;

}
