<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function register(string $email, string $password, string $hero): array
    {
        $user = User::create([
            'email' => $email,
            'password' => Hash::make($password),
            'hero' => $hero,
        ]);

        $token = $user->createToken('appToken')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(string $email, string $password)
    {
        $user = User::firstWhere('email', $email);

        if (!$user || !Hash::check($password, $user->password)) {
            return false;
        }

        $user->tokens()->delete();

        $token = $user->createToken('appToken')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
    }

    public function update(User $user, string $email)
    {
        $user->update([
            'email' => $email,
        ]);
    }
}
