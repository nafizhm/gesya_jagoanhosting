<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiKavling extends Model
{
    use HasFactory;

    protected $table = 'lokasi_kavling';

    protected $primaryKey = 'id_lokasi';

    public $timestamps = false;

    protected $fillable = [
        'id_lokasi',
        'nama_kavling',
        'nama_singkat',
        'alamat',
        'urutan',
        'header',
        'stt_tampil',
        'is_cluster'
    ];

    public function kavlingPeta()
    {
        return $this->hasMany(KavlingPeta::class, 'id_lokasi', 'id_lokasi');
    }
    public function masterSvg()
    {
        return $this->hasOne(MasterSVG::class, 'id_lokasi', 'id_lokasi');
    }
    public function perusahaan()
    {
        return $this->belongsToMany(
            Perusahaan::class,
            'kavling_perusahaan',
            'id_lokasi',
            'id_perusahaan'
        );
    }
    public function hakAkses()
    {
        return $this->hasMany(HakAkses::class, 'id_menu')->onDelete('cascade');
    }
}
