<?php

namespace App\Services\Auth;

use App\Http\Requests\EmailRegistrationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class EmailService
{
    /**
     * Register user via email
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return string
     */
    public function register(string $name, string $email, string $password): string
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        return $this->createToken($user);
    }

    /**
     * Create user token
     *
     * @param User $user
     * @return string
     */
    public function createToken(User $user): string
    {
        return $user->createToken('access_token')->plainTextToken;
    }
}
