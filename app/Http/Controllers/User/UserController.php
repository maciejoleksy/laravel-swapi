<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use Illuminate\Contracts\Cache\Factory as CacheRepository;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
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

    public function update(UpdateRequest $request)
    {
        $user = auth()->user();

        return $this->userRepository->update(
            $user,
            $request->input('email')
        );
    }

    public function getFilmsByHeroName()
    {
        $user = auth()->user();

        if (!$this->cacheRepository->get('films' . $user->hero)) {
            $response = Http::get($this->swapi . 'people/?search=' . $user->hero);
            $this->cacheRepository->add('films' . $user->hero, $this->getDecodedResponse($response), now()->addDay());
        }

        $response = $this->cacheRepository->get('films' . $user->hero);

        $response = collect($response['results'])->mapWithKeys(function ($result) {
            $films = collect($result['films'])->map(function ($film) {
                if (!$this->cacheRepository->get($film)) {
                    $response = Http::get($film);
                    $this->cacheRepository->add($film, $this->getDecodedResponse($response), now()->addDay());
                }

                return $this->cacheRepository->get($film);
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

    public function getPlanetsByHeroName()
    {
        $user = auth()->user();

        if (!$this->cacheRepository->get('films' . $user->hero)) {
            $response = Http::get($this->swapi . 'people/?search=' . $user->hero);
            $this->cacheRepository->add('films' . $user->hero, $this->getDecodedResponse($response), now()->addDay());
        }

        $response = $this->cacheRepository->get('films' . $user->hero);

        $response = collect($response['results'])->mapWithKeys(function ($result) {
            $planets = collect($result['homeworld'])->map(function ($planet) {
                if (!$this->cacheRepository->get($planet)) {
                    $response = Http::get($planet);
                    $this->cacheRepository->add($planet, $this->getDecodedResponse($response), now()->addDay());
                }

                return $this->cacheRepository->get($planet);
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

    public function getResources(string $resource, int $id)
    {
        $user = auth()->user();

        if (!$this->cacheRepository->get($resource . '/' . $id)) {
            $response = Http::get($this->swapi . $resource . '/' . $id);
            $this->cacheRepository->add($resource . '/' . $id, $this->getDecodedResponse($response), now()->addDay());
        }

        $response = $this->cacheRepository->get($resource . '/' . $id);

        $response = collect($response['people'])->map(function ($hero) {
            if (!$this->cacheRepository->get($hero)) {
                $response = Http::get($hero);
                $this->cacheRepository->add($hero, $this->getDecodedResponse($response), now()->addDay());
            }

            return $this->cacheRepository->get($hero)['name'];
        });

        $heroName = array_search($user->hero, $response->toArray());

        if ($heroName === false) {
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

    private function getDecodedResponse($response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
