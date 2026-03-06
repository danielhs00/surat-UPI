<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Modules\Mahasiswa\Models\StudentDocument;

class StudentDocumentPolicy
{
    public function view(User $user, StudentDocument $doc): bool
    {
        return $doc->user_id === $user->id;
    }

    public function update(User $user, StudentDocument $doc): bool
    {
        return $doc->user_id === $user->id;
    }

    public function delete(User $user, StudentDocument $doc): bool
    {
        return $doc->user_id === $user->id;
    }
}
