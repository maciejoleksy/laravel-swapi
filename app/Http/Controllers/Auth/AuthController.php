<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Contracts\SwapiInterface;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private UserRepositoryInterface $userRepository;

    private SwapiInterface $swapi;

    public function __construct(
        UserRepositoryInterface $userRepository,
        SwapiInterface $swapi
    )
    {
        $this->userRepository = $userRepository;
        $this->swapi = $swapi;
    }

    public function register(RegisterRequest $request)
    {
        $register = $this->userRepository->register(
            $request->input('email'),
            $request->input('password'),
            $this->swapi->getRandomHeroName()
        );

        return $this->success($register);
    }

    public function login(LoginRequest $request)
    {
        $login = $this->userRepository->login(
            $request->input('email'),
            $request->input('password'),
        );

        if (!$login) {
            return $this->error(401, 'Wrong data.');
        }

        return $this->success($login);
    }

    public function logout()
    {
        $this->userRepository->logout();

        return $this->success();
    }
}
