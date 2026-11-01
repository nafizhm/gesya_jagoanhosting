<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadFile extends Model
{
    protected $table = 'file_nasabah';

    protected $primaryKey = 'id_file';

    protected $fillable = [
        'tanggal',
        'nasabah',
        'folder',
        'nama_file',
        'keterangan',
        'lampiran',
    ];

    public $timestamps = false;
}
