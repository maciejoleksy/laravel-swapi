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

class UserRepository implements UserRepositoryInterface
{
    public function register(RegisterRequest $request)
    {
        $response = Http::get('https://swapi.dev/api/people');
        $response = $this->getDecodedResponse($response);

        $heroId = rand(1, $response['count']);

        $response = Http::get('https://swapi.dev/api/people/' .$heroId);
        $response = $this->getDecodedResponse($response);

        $heroName = $response['name'];

        $user = User::create([
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'hero'     => $heroName,
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
        $response = Http::get('https://swapi.dev/api/people/?search=' .$user->hero);
        $response = $this->getDecodedResponse($response);

        $response = collect($response['results'])->mapWithKeys(function ($result) {
            $films = collect($result['films'])->map(function ($film) {
                $response = Http::get($film);
                $response = $this->getDecodedResponse($response);

                return $response;
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
        $response = Http::get('https://swapi.dev/api/people/?search=' .$user->hero);
        $response = $this->getDecodedResponse($response);

        $response = collect($response['results'])->mapWithKeys(function ($result) {
            $planets = collect($result['homeworld'])->map(function ($planet) {
                $response = Http::get($planet);
                $response = $this->getDecodedResponse($response);

                return $response;
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
