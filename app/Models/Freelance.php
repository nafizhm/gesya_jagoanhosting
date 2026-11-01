<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Freelance extends Model
{
    use HasFactory;

    protected $table = 'freelance';
    protected $primaryKey = 'id_freelance';

    protected $fillable = [
        'kode_freelance',
        'nama_freelance',
        'jenis_kelamin',
        'alamat',
        'tempat_lahir',
        'pekerjaan',
        'no_telp_freelance',
        'foto',
        'status',
        'nama_bank',
        'no_rekening',
        'atas_nama',
        'id_level',
        'stt_freelance',
        'tempat_lahir',
    ];

    public $timestamps = false;
}
