<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use App\Contracts\Helpers\Cache;
use App\Contracts\Helpers\Swapi;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
    ) {
        $this->userRepository  = $userRepository;
        $this->swapi           = config('swapi.base_uri');
    }

    public function update(UpdateRequest $request)
    {
        $user = auth()->user();

        $this->userRepository->update(
            $user,
            $request->input('email')
        );

        return response()->json([
            'message' => 'Email changed.'
        ], 200);
    }

    public function getFilmsByHeroName(Cache $cache, Swapi $swapi)
    {
        $user = auth()->user();

        $response = $cache->getOrSet('films' . $user->hero, function() use ($user, $swapi) {
            return $swapi->getResponse($this->swapi . 'people/?search=' . $user->hero);
        });

        $response = collect($response['results'])->mapWithKeys(function ($result) use ($cache, $swapi) {
            $films = collect($result['films'])->map(function ($film) use ($cache, $swapi) {
                return $cache->getOrSet($film, function() use ($film, $swapi) {
                    return $swapi->getResponse($film);
                });
            });

            return [
                'films' => $films,
            ];
        });

        return response()->json([
            'message' => 'Success.',
            'results' => $response,
        ], 200);
    }

    public function getPlanetsByHeroName(Cache $cache, Swapi $swapi)
    {
        $user = auth()->user();

        $response = $cache->getOrSet('planets' . $user->hero, function() use ($user, $swapi) {
            return $swapi->getResponse($this->swapi . 'people/?search=' . $user->hero);
        });

        $response = collect($response['results'])->mapWithKeys(function ($result) use ($cache) {
            $planets = collect($result['homeworld'])->map(function ($planet) use ($cache) {
                return $cache->getOrSet($planet, function() use ($planet) {
                    return $swapi->getResponse($planet);
                });
            });

            return [
                'planets' => $planets,
            ];
        });

        return response()->json([
            'message' => 'Success.',
            'results' => $response,
        ], 200);
    }

    public function getResources(string $resource, int $id, Cache $cache, Swapi $swapi)
    {
        $user = auth()->user();

        $response = $cache->getOrSet($resource . '/' . $id, function() use ($id, $swapi, $resource) {
            return $swapi->getResponse($this->swapi . $resource . '/' . $id);
        });

        $response = collect($response['people'])->map(function ($hero) use ($cache, $swapi) {
            $response = $cache->getOrSet($hero, function() use ($hero, $swapi) {
                return $swapi->getResponse($hero);
            });
            
            return $response['name'];
        });
        
        $heroName = array_search($user->hero, $response->toArray());

        if (!$heroName) {
            return response()->json([
                'message' => 'Forbidden.'
            ], 403);
        }

        $response = $this->cacheRepository->get($resource . '/' . $id);

        return response()->json([
            'message' => 'Success.',
            'results' => $response,
        ], 200);
    }
}
