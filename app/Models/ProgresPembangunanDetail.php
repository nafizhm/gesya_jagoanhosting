<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgresPembangunanDetail extends Model
{
    use HasFactory;

    protected $table = 'progres_pembangunan_detail';
    protected $primaryKey = 'id_progres_pembangunan';

    protected $fillable = [
        'tgl_update',
        'id_kavling',
        'id_list_pembangunan',
        'keterangan_progres',

    ];

    public function updates()
    {
        return $this->hasMany(ProgresPembangunanDetail::class, 'id_kavling', 'id_kavling')
            ->join('progres_list_pembangunan', 'progres_pembangunan_detail.id_list_pembangunan', '=', 'progres_list_pembangunan.id_list_pembangunan')
            ->orderBy('progres_list_pembangunan.persentase', 'asc')
            ->select('progres_pembangunan_detail.*');
    }

    public function kavling()
{
    return $this->belongsTo(KavlingPeta::class, 'id_kavling', 'id_kavling');
}

public function listProgres()
{
    return $this->belongsTo(ProgresPembangunan::class, 'id_list_pembangunan', 'id_list_pembangunan');
}

public function lokasi()
{
    return $this->belongsTo(LokasiKavling::class, 'id_lokasi', 'id_lokasi');
}

    public $timestamps = false;
}
