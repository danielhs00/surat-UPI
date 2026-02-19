<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Master\Database\Factories\FakultasFactory;

class Fakultas extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
     protected $table = 'fakultas';
    protected $fillable = [
        'nama_fakultas',
        'kode_fakultas',
    ];

     public function mahasiswa()
    {
        return $this->hasMany(\App\Models\Mahasiswa::class);
    }

    public function operator()
    {
        return $this->hasOne(\App\Models\Operator::class);
    }

    public function wadek()
    {
        return $this->hasOne(\App\Models\Wadek::class);
    }

    // protected static function newFactory(): FakultasFactory
    // {
    //     // return FakultasFactory::new();
    // }
}
