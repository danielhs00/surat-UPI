<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $fillable = ['user_id','nim','prodi','angkatan'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fakultas()
    {
        return $this->belongsTo(\Modules\Master\Models\Fakultas::class);
    }
}
