<?php

namespace App\Contracts\Repositories;
use App\Models\User;

interface UserRepositoryInterface
{
    public function store(array $data);

    public function firstUserByEmail(string $email);
}