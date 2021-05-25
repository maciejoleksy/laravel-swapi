<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Contracts\Helpers\Cache as CacheRepository;
use App\Contracts\Helpers\Swapi as SwapiRepository;

class AuthController extends Controller
{
    private $userRepository;
    
    private $cacheRepository;

    private $swapiRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CacheRepository $cacheRepository,
        SwapiRepository $swapiRepository
    ) {
        $this->userRepository  = $userRepository;
        $this->cacheRepository = $cacheRepository;
        $this->swapiRepository = $swapiRepository;
        $this->swapiUrl        = config('swapi.base_url');
    }

    public function register(RegisterRequest $request)
    {
        $response = $this->cacheRepository->getOrSet('people', function() {
            return $this->swapiRepository->getResponse($this->swapiUrl . 'people');
        });

        $hero = rand(1, $response['count']);

        $response = $this->cacheRepository->getOrSet('people' . $hero, function() use ($hero) {
            return $this->swapiRepository->getResponse($this->swapiUrl . 'people/' . $hero);
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
        $this->userRepository->logout();

        return response()->json([
            'message' => 'Logout.'
        ], 200);
    }
}
