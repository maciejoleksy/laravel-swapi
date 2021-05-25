<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use App\Contracts\Helpers\Cache as CacheRepository;
use App\Contracts\Helpers\Swapi as SwapiRepository;
use App\Contracts\Helpers\ApiResponse;

class UserController extends Controller
{
    private $userRepository;
    
    private $cacheRepository;

    private $swapiRepository;

    private $apiResponse;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CacheRepository $cacheRepository,
        SwapiRepository $swapiRepository,
        ApiResponse $apiResponse
    ) {
        $this->userRepository  = $userRepository;
        $this->cacheRepository = $cacheRepository;
        $this->swapiRepository = $swapiRepository;
        $this->apiResponse     = $apiResponse;
        $this->swapiUrl        = config('swapi.base_url');
    }

    public function update(UpdateRequest $request)
    {
        $user = auth()->user();

        $this->userRepository->update(
            $user,
            $request->input('email')
        );
        
        return $this->apiResponse->success();
    }

    public function getFilmsByHeroName()
    {
        $user = auth()->user();

        $response = $this->cacheRepository->getOrSet('films' . $user->hero, function() use ($user) {
            return $this->swapiRepository->getResponse($this->swapiUrl . 'people/?search=' . $user->hero);
        });

        $response = collect($response['results'])->mapWithKeys(function ($result) {
            $films = collect($result['films'])->map(function ($film) {
                return $this->cacheRepository->getOrSet($film, function() use ($film) {
                    return $this->swapiRepository->getResponse($film);
                });
            });

            return [
                'films' => $films,
            ];
        });

        return $this->apiResponse->success($response);
    }

    public function getPlanetsByHeroName()
    {
        $user = auth()->user();

        $response = $this->cacheRepository->getOrSet('planets' . $user->hero, function() use ($user) {
            return $this->swapiRepository->getResponse($this->swapiUrl . 'people/?search=' . $user->hero);
        });

        $response = collect($response['results'])->mapWithKeys(function ($result) {
            $planets = collect($result['homeworld'])->map(function ($planet) {
                return $this->cacheRepository->getOrSet($planet, function() use ($planet) {
                    return $this->swapiRepository->getResponse($planet);
                });
            });

            return [
                'planets' => $planets,
            ];
        });

        return $this->apiResponse->success($response);
    }

    public function getResources(string $resource, int $id)
    {
        $user = auth()->user();

        $resource = $this->cacheRepository->getOrSet($resource . '/' . $id, function() use ($resource, $id) {
            return $this->swapiRepository->getResponse($this->swapiUrl . $resource . '/' . $id);
        });

        $response = collect($resource['people'])->map(function ($hero) {
            $response = $this->cacheRepository->getOrSet($hero, function() use ($hero) {
                return $this->swapiRepository->getResponse($hero);
            });
            
            return $response['name'];
        });
        
        $heroName = array_search($user->hero, $response->toArray());

        if (!$heroName) {
            return $this->apiResponse->error(401, 'Unauthorized.');
        }

        return $this->apiResponse->success($resource);
    }
}
