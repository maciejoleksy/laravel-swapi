<?php

namespace App\Contracts;

use Closure;

interface CacheInterface
{
    public function getOrSet(string $key, closure $callback);
}
