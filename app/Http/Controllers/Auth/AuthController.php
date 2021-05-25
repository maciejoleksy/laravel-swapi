<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Contracts\Helpers\Cache;
use App\Contracts\Helpers\Swapi;

class AuthController extends Controller
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
    ) {
        $this->userRepository  = $userRepository;
        $this->swapi           = config('swapi.base_uri');
    }

    public function register(RegisterRequest $request, Cache $cache, Swapi $swapi)
    {
        $response = $cache->getOrSet('people', function() use ($swapi) {
            return $swapi->getResponse($this->swapi . 'people');
        });

        $hero = rand(1, $response['count']);

        $response = $cache->getOrSet('people' . $hero, function() use ($hero, $swapi) {
            return $swapi->getResponse($this->swapi . 'people/' . $hero);
        });

        $hero = $response['name'];

        $register = $this->userRepository->register(
            $request->input('email'),
            $request->input('password'),
            $hero
        );

        return response()->json([
            'message' => 'User created.',
            'results' => $register,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $login = $this->userRepository->login(
            $request->input('email'),
            $request->input('password'),
        );
        
        if (!$login) {
            return response()->json([
                'message' => 'Wrong data.'
            ], 401);
        }

        return response()->json([
            'message' => 'User login.',
            'results' => $login,
        ], 200);
    }

    public function logout()
    {
        $logout = $this->userRepository->logout();

        return response()->json([
            'message' => 'Logout.',
            'results' => $logout,
        ], 200);
    }
}
