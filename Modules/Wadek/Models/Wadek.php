<?php

namespace Modules\Wadek\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Wadek extends Model
{
    protected $table = 'wadek';

    protected $fillable = [
        'user_id',
        'fakultas_id',
        'ttd_path',
        'ttd_uploaded_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fakultas()
    {
        return $this->belongsTo(\Modules\Master\Models\Fakultas::class, 'fakultas_id');
    }
}