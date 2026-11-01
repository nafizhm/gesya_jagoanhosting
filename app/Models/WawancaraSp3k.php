<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WawancaraSp3k extends Model
{
    use HasFactory;

    protected $table = 'wawancara_sp3k';

    protected $primaryKey = 'id_sp3k';

    protected $fillable = [
        'id_wawancara',
        'id_bank',
        'acc_plafon',
        'tenor',
        'id_notaris',
        'tgl_terbit_sp3k',
        'tgl_expired',
        'catatan_acc',
        'stt_arsip',
    ];

    public $timestamps = false;

    public function bank()
    {
        return $this->belongsTo(BankModel::class, 'id_bank', 'id_bank');
    }

    public function notaris()
    {
        return $this->belongsTo(Notaris::class, 'id_notaris', 'id_notaris');
    }

    public function wawancara()
    {
        return $this->belongsTo(Wawancara::class, 'id_wawancara', 'id_wawancara');
    }
}
