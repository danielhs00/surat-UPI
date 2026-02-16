<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class wadek extends Model
{
     protected $table = 'wadek'; // sesuaikan kalau tabel kamu 'wadeks'

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
    ];
}
