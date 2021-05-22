<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Contracts\Repositories\UserRepositoryInterface;

class AuthController extends Controller
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        return $this->userRepository = $userRepository;
    }

    public function register(RegisterRequest $request)
    {
        return $this->userRepository->register($request);
    }

    public function login(LoginRequest $request)
    {
        return $this->userRepository->login($request);
    }

    public function logout()
    {
        return $this->userRepository->logout();
    }
}
