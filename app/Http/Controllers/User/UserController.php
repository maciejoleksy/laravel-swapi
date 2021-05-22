<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use Illuminate\Support\Facades\Http;
use App\Contracts\Repositories\UserRepositoryInterface;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        return $this->userRepository = $userRepository;
    }

    public function update(UpdateRequest $request)
    {
        $user = auth()->user();

        return $this->userRepository->update($user, $request);
    }

    public function getFilmsByHeroName()
    {
        $user = auth()->user();

        return $this->userRepository->getFilmsByHeroName($user);
    }

    public function getPlanetsByHeroName()
    {
        $user = auth()->user();

        return $this->userRepository->getPlanetsByHeroName($user);
    }
}
