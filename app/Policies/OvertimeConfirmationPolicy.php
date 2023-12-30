<?php

namespace App\Policies;

use App\Models\OvertimeConfirmation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OvertimeConfirmationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, OvertimeConfirmation $overtimeConfirmation): bool
    {
        return $user->id === $overtimeConfirmation->user_id;
    }
}
