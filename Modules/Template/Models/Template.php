<?php

namespace Modules\Template\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Template\Database\Factories\TemplateFactory;

class Template extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'templates';
    protected $fillable = [
        'fakultas_id',
        'uploaded_by',
        'nama_template',
        'jenis_surat',
        'deskripsi',
        'google_docs_url',
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
    // protected static function newFactory(): TemplateFactory
    // {
    //     // return TemplateFactory::new();
    // }
}
