<?php

namespace App\Contracts;

interface SwapiInterface
{
    public function getResponse(string $url);

    public function getRandomHero();

    public function getRandomHeroName();

    public function getFilms(string $hero);

    public function getPlanets(string $hero);

    public function hasPermissions(string $resource, int $id, string $hero);

    public function getResources(string $resource, int $id);
}
