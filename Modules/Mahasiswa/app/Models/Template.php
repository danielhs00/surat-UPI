<?php

namespace Modules\Mahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Mahasiswa\Database\Factories\TemplateFactory;

class Template extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name','description','docx_path','is_active'
    ];

    // protected static function newFactory(): TemplateFactory
    // {
    //     // return TemplateFactory::new();
    // }
}
