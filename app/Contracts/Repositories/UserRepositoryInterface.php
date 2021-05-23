<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\User\UpdateRequest;

interface UserRepositoryInterface
{
    public function register(RegisterRequest $request);

    public function login(LoginRequest $request);

    public function logout();

    public function update(User $user, UpdateRequest $request);

    public function getFilmsByHeroName(User $user);

    public function getPlanetsByHeroName(User $user);
}
