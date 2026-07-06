<?php

namespace App\Policies;

use App\Models\Gear;
use App\Models\User;

class GearPolicy
{
    public function update(User $user, Gear $gear): bool
    {
        return $gear->user_id === $user->id;
    }

    public function delete(User $user, Gear $gear): bool
    {
        return $gear->user_id === $user->id;
    }
}
