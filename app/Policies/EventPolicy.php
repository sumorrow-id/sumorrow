<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function delete(User $user, Event $event): bool
    {
        return $event->community->isMember($user)
            && ($event->user_id === $user->id || $event->community->isCreatedBy($user));
    }
}
