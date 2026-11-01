<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgresPembangunan extends Model
{
    use HasFactory;

    protected $table = 'progres_list_pembangunan';
    protected $primaryKey = 'id_list_pembangunan';

    protected $fillable = [
        'persentase',
        'keterangan',
        'warna',

    ];

    public $timestamps = false;
}
