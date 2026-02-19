<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class operator extends Model
{
    protected $table = 'operator';
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fakultas()
    {
        return $this->belongsTo(\Modules\Master\Models\Fakultas::class);
    }
}
