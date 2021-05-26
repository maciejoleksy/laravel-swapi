<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\SwapiInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use App\Contracts\Helpers\Cache as CacheRepository;
use App\Contracts\Helpers\Swapi as SwapiRepository;
use App\Contracts\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
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

    public function update(UpdateRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $this->userRepository->update(
            $user,
            $request->input('email')
        );

        return $this->success();
    }

    public function getFilmsByHeroName()
    {
        /** @var User $user */
        $user = auth()->user();

        return $this->success([
            'films' => $this->swapi->getFilms($user->hero)
        ]);
    }

    public function getPlanetsByHeroName()
    {
        /** @var User $user */
        $user = auth()->user();

        return $this->success([
            'films' => $this->swapi->getPlanets($user->hero)
        ]);
    }

    public function getResources(string $resource, int $id)
    {
        /** @var User $user */
        $user = auth()->user();

        $hasPermissions = $this->swapi->hasPermissions($resource, $id, $user->hero);

        if (!$hasPermissions) {
            return $this->error(401, 'Unauthorized.');
        }

        return $this->success([
            'resources' => $this->swapi->getResources($resource, $id),
        ]);
    }
}
