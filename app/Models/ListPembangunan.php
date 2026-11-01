<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListPembangunan extends Model
{
    //
    use HasFactory;

    protected $table = 'progres_list_pembangunan';

    protected $primaryKey = 'id_list_pembangunan';

    protected $fillable = [
        'keterangan',
        'warna'

    ];

    public $timestamps = false;

}
