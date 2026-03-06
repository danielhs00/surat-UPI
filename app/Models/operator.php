<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    protected $table = 'operator';

    protected $fillable = [
        'user_id',
        'fakultas_id',
        'prodi_id',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fakultas()
    {
        return $this->belongsTo(\Modules\Master\Models\Fakultas::class, 'fakultas_id');
    }

    public function prodi()
    {
        // karena kamu belum punya Model Prodi, kita bisa pakai query builder di blade,
        // TAPI lebih rapi bikin model Prodi. Kalau belum mau, minimal bikin model sederhana.

        return $this->belongsTo(\App\Models\Prodi::class, 'prodi_id');
    }
}