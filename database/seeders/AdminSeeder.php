<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Promote every email in the ADMIN_EMAILS env var (comma-separated) to
     * admin, creating the account first when it does not exist yet so the
     * owner can claim it via Google login (matched by email). Idempotent and
     * safe to run on every deploy; never demotes anyone.
     */
    public function run(): void
    {
        $emails = array_filter(array_map('trim', explode(',', (string) config('auth.admin_emails'))));

        foreach ($emails as $email) {
            $username = strstr($email, '@', true) ?: $email;

            if (User::where('username', $username)->where('email', '!=', $email)->exists()) {
                $username = $email;
            }

            User::firstOrCreate(['email' => $email], [
                'username' => $username,
                'password_hash' => null,
            ])->forceFill(['role' => 'admin'])->save();
        }
    }
}
