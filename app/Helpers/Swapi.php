<?php

namespace App\Helpers;

use App\Contracts\CacheInterface;
use App\Contracts\SwapiInterface;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Swapi implements SwapiInterface
{
    private string $swapiUrl;

    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->swapiUrl = config('swapi.base_url');
    }

    public function getResponse(string $url)
    {
        try {
            $response = Http::get($url);

            return $this->getDecodedResponse($response);
        } catch (Exception $exception) {
            return [];
        }
    }

    public function getRandomHero()
    {
        $response = $this->cache->getOrSet('people', function () {
            return $this->getResponse($this->swapiUrl . 'people');
        });

        $hero = rand(1, $response['count']);

        return $this->cache->getOrSet($this->swapiUrl . 'people/' . $hero, function () use ($hero) {
            return $this->getResponse($this->swapiUrl . 'people/' . $hero);
        });
    }

    public function getRandomHeroName()
    {
        $response = $this->getRandomHero();

        return $response['name'];
    }

    public function getFilms(string $hero)
    {
        $response = $this->cache->getOrSet('people' . $hero, function () use ($hero) {
            return $this->getResponse($this->swapiUrl . 'people/?search=' . $hero);
        });

        return collect(Arr::get($response, 'results', []))->mapWithKeys(function ($result) {
            return collect(Arr::get($result, 'films', []))->map(function ($film) {
                return $this->cache->getOrSet($film, function () use ($film) {
                    return $this->getResponse($film);
                });
            });
        });
    }

    public function getPlanets(string $hero)
    {
        $response = $this->cache->getOrSet('people' . $hero, function () use ($hero) {
            return $this->getResponse($this->swapiUrl . 'people/?search=' . $hero);
        });

        return collect(Arr::get($response, 'results', []))->mapWithKeys(function ($result) {
            return collect(Arr::get($result, 'homeworld', []))->map(function ($planet) {
                return $this->cache->getOrSet($planet, function () use ($planet) {
                    return $this->getResponse($planet);
                });
            });
        });
    }

    public function getResources(string $resource, int $id)
    {
        return $this->cache->getOrSet($resource . '/' . $id, function () use ($resource, $id) {
            return $this->getResponse($this->swapiUrl . $resource . '/' . $id);
        });
    }

    public function hasPermissions(string $resource, int $id, string $hero)
    {
        $resources = $this->getResources($resource, $id);

        $response = collect(Arr::get($resources, 'people', []))->map(function ($person) {
            $response = $this->cache->getOrSet($person, function () use ($person) {
                return $this->getResponse($person);
            });
            return Arr::get($response, 'name');
        });

        return false !== array_search($hero, $response->toArray());
    }

    protected function getDecodedResponse($response)
    {
        return (array)json_decode($response->getBody()->getContents(), true);
    }
}
