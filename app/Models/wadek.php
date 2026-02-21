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

    public function fakultas()
    {
        return $this->belongsTo(\Modules\Master\Models\Fakultas::class);
    }
}
