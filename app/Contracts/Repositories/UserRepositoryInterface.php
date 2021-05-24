<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function register(string $email, string $password, string $hero);

    public function login(string $email, string $password);

    public function logout();

    public function update(User $user, string $email);
}
