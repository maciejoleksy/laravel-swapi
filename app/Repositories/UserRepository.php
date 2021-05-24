<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function register(string $email, string $password, string $hero)
    {
        $user = User::create([
            'email'    => $email,
            'password' => Hash::make($password),
            'hero'     => $hero,
        ]);

        $token = $user->createToken('appToken')->plainTextToken;

        $response = [
            'user'  => $user,
            'token' => $token,
        ];

        return response()->json([
            'message' => 'User ' . $user->email . ' created.',
            'results' => $response,
        ], 201);
    }

    public function login(string $email, string $password)
    {
        $user = User::firstWhere('email', $email);

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Wrong data.',
                'results' => $response,
            ], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('appToken')->plainTextToken;

        $response = [
            'user'  => $user,
            'token' => $token,
        ];

        return response()->json([
            'message' => 'User ' . $user->email . ' login.',
            'results' => $response,
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout.',
            'results' => $response,
        ], 200);
    }

    public function update(User $user, string $email)
    {
        $user->update([
            'email' => $email,
        ]);

        return response()->json([
            'message' => 'Email changed.',
        ], 200);
    }
}
