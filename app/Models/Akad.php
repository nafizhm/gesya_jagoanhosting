<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Akad extends Model
{
    use HasFactory;

    protected $table = 'akad';

    protected $primaryKey = 'id_akad';
    public $timestamps = false;

    protected $fillable = [
        'tgl_akad',
        'keterangan',
        'stt_delete',
    ];

    public function detail()
    {
        return $this->hasMany(AkadDetail::class, 'id_akad');
    }


}
