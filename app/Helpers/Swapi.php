<?php

namespace App\Helpers;

use App\Contracts\CacheInterface;
use App\Contracts\SwapiInterface;
use Exception;
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
        // TODO: Implement getFilms() method.
    }

    public function getPlanets(string $hero)
    {
        // TODO: Implement getPlanets() method.
    }

    public function hasPermissions(string $resource, int $id, string $hero)
    {
        // TODO: Implement hasPermissions() method.
    }

    public function getResources(string $resource, int $id)
    {
        // TODO: Implement getResources() method.
    }

    private function getDecodedResponse($response)
    {
        return (array)json_decode($response->getBody()->getContents(), true);
    }
}
