<?php

namespace Modules\Template\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Template extends Model
{
    use HasFactory;

    protected $table = 'templates';
    
    protected $fillable = [
        'fakultas_id',
        'uploaded_by',
        'nama_template',
        'jenis_surat',
        'deskripsi',
        'file_docx_path', 
        'is_active'
    ];

    public function fakultas()
    {
        return $this->belongsTo(\Modules\Master\Models\Fakultas::class, 'fakultas_id');
    }

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}