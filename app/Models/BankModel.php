<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankModel extends Model
{
    use HasFactory;
    protected $table = 'bank';

    protected $primaryKey = 'id_bank';

    protected $fillable = [
        'kode_bank',
        'bank',
        'keterangan_bank',
    ];

    public $timestamps = false;
}
