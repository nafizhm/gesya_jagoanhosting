<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanPesan extends Model
{
    //
    use HasFactory;

    protected $table = 'template';

    protected $primaryKey = 'id_template';

    protected $fillable = [
        'nama_template',
        'isi_template',
        'jenis_pesan',

    ];

    public $timestamps = false;

}
