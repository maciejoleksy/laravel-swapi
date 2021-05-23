<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class UserRepository implements UserRepositoryInterface
{
    public function register(RegisterRequest $request)
    {
        if (!Cache::get('people')) {
            $response = Http::get('https://swapi.dev/api/people');
            Cache::add('people', $this->getDecodedResponse($response), now()->addDay());
        }

        $response = Cache::get('people');
        $heroId   = rand(1, $response['count']);

        if (!Cache::get('people' .$heroId)) {
            $response = Http::get('https://swapi.dev/api/people/' .$heroId);
            Cache::add('people' .$heroId, $this->getDecodedResponse($response), now()->addDay());
        }

        $response = Cache::get('people' .$heroId);
        $hero     = $response['name'];

        $user = User::create([
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'hero'     => $hero,
        ]);

        $token = $user->createToken('appToken')->plainTextToken;

        $response = [
            'user'=> $user,
            'token' => $token
        ];

        return response()->json([
            'message' => 'User '.$user->email.' created.',
            'results' => $response
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::firstWhere('email', $request->input('email'));

        if(!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'Wrong data.',
                'results' => $response
            ], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('appToken')->plainTextToken;

        $response = [
            'user'  => $user,
            'token' => $token,
        ];

        return response()->json([
            'message' => 'User '.$user->email.' login.',
            'results' => $response
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout.',
            'results' => $response
        ], 200);
    }

    public function update(User $user, UpdateRequest $request)
    {
        $user->update([
            'email' => $request->input('email'),
        ]);

        return response()->json([
            'message' => 'Email changed.',
        ], 200);
    }

    public function getFilmsByHeroName(User $user)
    {
        if (!Cache::get('films' .$user->hero)) {
            $response = Http::get('https://swapi.dev/api/people/?search=' .$user->hero);
            Cache::add('films' .$user->hero, $this->getDecodedResponse($response), now()->addDay());
        }

        $response = Cache::get('films' .$user->hero);

        $response = collect($response['results'])->mapWithKeys(function ($result) {
            $films = collect($result['films'])->map(function ($film) {
                if (!Cache::get($film)) {
                    $response = Http::get($film);
                    Cache::add($film, $this->getDecodedResponse($response), now()->addDay());
                }

                return Cache::get($film);
            });

            return [
                'films' => $films,
            ];
        });

        return response()->json([
            'message' => 'Success.',
            'results' => $response
        ], 200);
    }

    public function getPlanetsByHeroName(User $user)
    {
        if (!Cache::get('planets' .$user->hero)) {
            $response = Http::get('https://swapi.dev/api/people/?search=' .$user->hero);
            Cache::add('planets' .$user->hero, $this->getDecodedResponse($response), now()->addDay());
        }

        $response = Cache::get('planets' .$user->hero);

        $response = collect($response['results'])->mapWithKeys(function ($result) {
            $planets = collect($result['homeworld'])->map(function ($planet) {
                if (!Cache::get($planet)) {
                    $response = Http::get($planet);
                    Cache::add($planet, $this->getDecodedResponse($response), now()->addDay());
                }

                return Cache::get($planet);
            });

            return [
                'planets' => $planets,
            ];
        });

        return response()->json([
            'message' => 'Success.',
            'results' => $response
        ], 200);
    }

    private function getDecodedResponse($response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
