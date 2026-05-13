<?php

namespace App\Contracts;

use App\Models\User;

interface SocialAuthInterface
{
    public function findOrCreateUser(object $social): User; 
}
