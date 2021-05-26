<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface SwapiInterface
{
    public function getResponse(string $url);

    public function getRandomHero();

    public function getRandomHeroName();

    public function getFilms(string $hero);

    public function getPlanets(string $hero);

    public function getResources(string $resource, int $id);

    public function hasPermissionsByPeople(string $resource, int $id, string $hero);

    public function hasPermissionsByPilots(string $resource, int $id, string $hero);

    public function hasPermissionsByResidents(string $resource, int $id, string $hero);

    public function hasPermissionsByCharacters(string $resource, int $id, string $hero);

    public function hasPermissionsByName(string $resource, int $id, string $hero);
}
