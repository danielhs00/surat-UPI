<?php

namespace Modules\Mahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Mahasiswa\Models\Template;
// use Modules\Mahasiswa\Database\Factories\StudentDocumentFactory;

class StudentDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'template_id',
        'title',
        'docx_path',
        'pdf_path',
        'status',
        'submitted_at',
        'converted_at',
        'convert_error'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
