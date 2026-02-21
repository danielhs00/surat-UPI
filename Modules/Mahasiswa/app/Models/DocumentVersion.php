<?php

namespace Modules\Mahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Mahasiswa\Models\StudentDocument; 
// use Modules\Mahasiswa\Database\Factories\DocumentVersionFactory;

class DocumentVersion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_document_id','version','docx_path','pdf_path','note'
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(StudentDocument::class, 'student_document_id');
    }

}
