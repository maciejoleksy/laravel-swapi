<?php

namespace App\Helpers;

use App\Contracts\CacheInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Closure;
use Psr\SimpleCache\InvalidArgumentException;

class Cache implements CacheInterface
{
    private CacheRepository $cacheRepository;

    public function __construct(
        CacheRepository $cacheRepository
    )
    {
        $this->cacheRepository = $cacheRepository;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getOrSet(string $key, closure $callback)
    {
        if (!$this->cacheRepository->get($key)) {
            $data = $callback();
            $this->cacheRepository->add($key, $data, now()->addDay());
        }

        return $this->cacheRepository->get($key);
    }
}
