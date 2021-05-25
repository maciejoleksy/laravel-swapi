<?php

namespace App\Contracts\Helpers;

use Illuminate\Contracts\Cache\Factory as CacheRepository;
use Closure;

class Cache
{
    private $cacheRepository;

    public function __construct(
        CacheRepository $cacheRepository
    )
    {
        $this->cacheRepository = $cacheRepository;
    }

    public function getOrSet(string $key, closure $setCallback)
    {
        if (!$this->cacheRepository->get($key)) {
            $data = $setCallback();
            $this->cacheRepository->add($key, $data, now()->addDay());
        }

        return $this->cacheRepository->get($key);
    }
}
