<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function store(array $data)
    {
        return User::create([
            'email'    => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function firstUserByEmail(string $email)
    {
        return User::firstWhere('email', $email);
    }
}