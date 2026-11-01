<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengaturanPengguna extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'surname',
        'username',
        'password',
        'email',
        'status',
        'role'
    ];

    public $timestamps = false;

}
