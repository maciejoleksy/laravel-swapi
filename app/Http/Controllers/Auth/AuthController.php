<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Contracts\Cache\Factory as CacheRepository;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CacheRepository $cacheRepository
    ) {
        $this->userRepository  = $userRepository;
        $this->cacheRepository = $cacheRepository;
        $this->swapi           = config('swapi.base_uri');
    }

    public function register(RegisterRequest $request)
    {
        if (!$this->cacheRepository->get('people')) {
            $response = Http::get($this->swapi . 'people');
            $this->cacheRepository->add('people', $this->getDecodedResponse($response), now()->addDay());
        }

        $response = $this->cacheRepository->get('people');
        $hero     = rand(1, $response['count']);

        if (!$this->cacheRepository->get('people' . $hero)) {
            $response = Http::get($this->swapi . 'people/' . $hero);
            $this->cacheRepository->add('people' . $hero, $this->getDecodedResponse($response), now()->addDay());
        }

        $response = $this->cacheRepository->get('people' . $hero);
        $hero     = $response['name'];

        return $this->userRepository->register(
            $request->input('email'),
            $request->input('password'),
            $hero
        );
    }

    public function login(LoginRequest $request)
    {
        return $this->userRepository->login(
            $request->input('email'),
            $request->input('password'),
        );
    }

    public function logout()
    {
        return $this->userRepository->logout();
    }

    private function getDecodedResponse($response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
