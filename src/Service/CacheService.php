<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class CacheService
{
    private $cache;

    public function __construct(AdapterInterface $adapter)
    {
        $this->cache = $adapter;
    }

    public function cache(string $key, $query, $expire = 10800)
    {
        $item = $this->cache->getItem($key);
        if (!$item->isHit()) {
            $item->expiresAfter($expire);
            $item->set($query);
            $this->cache->save($item);
        }

        return $item->get();
    }
}
