<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wawancara extends Model
{
    use HasFactory;

    protected $table = 'wawancara';

    protected $primaryKey = 'id_wawancara';

    protected $fillable = [
        'tgl_wawancara',
        'id_customer',
        'id_kavling',
        'id_marketing',
        'catatan_kekurangan',
        'stt_wawancara',
        'id_bank',
    ];

    public $timestamps = false;

    public function nasabah()
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
    }

    public function kavling()
    {
        return $this->belongsTo(KavlingPeta::class, 'id_kavling', 'id_kavling');
    }

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'id_marketing', 'id_marketing');
    }

    public function bank()
    {
        return $this->belongsTo(BankModel::class, 'id_bank');
    }

    public function wawancaraSp3k()
    {
        return $this->hasOne(WawancaraSp3k::class, 'id_wawancara', 'id_wawancara');
    }
}
