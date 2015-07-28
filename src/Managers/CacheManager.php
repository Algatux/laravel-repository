<?php

namespace Algatux\Repository\Managers;

use Algatux\Repository\Eloquent\AbstractRepository;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CacheManager
{

    const DEFAULT_CACHE_LIFETIME = 1;

    /** @var  string */
    protected $cacheHashKeyPrefix;

    /** @var  int */
    protected $cacheLifeTime;

    /** @var CacheRepository */
    protected $cache;

    /** #var string */
    protected $actualQueryHash;

    /**
     * @param CacheRepository $cacheRepository
     */
    public function __construct(CacheRepository $cacheRepository)
    {

        $this->cache = $cacheRepository;
        $this->cacheLifeTime = self::DEFAULT_CACHE_LIFETIME;

    }

    /**
     * @param array $attributes
     * @return string
     */
    private function generateQueryHashFromQueryBuilder(array $attributes)
    {

        $haskKeyPrefix = $this->cacheHashKeyPrefix ? $this->cacheHashKeyPrefix : AbstractRepository::HASH_KEY_PREFIX;

        return sha1($haskKeyPrefix . "_" . serialize($attributes));

    }

    /**
     * @param array $attributes
     * @return LengthAwarePaginator|Collection|null
     */
    public function tryFetchResultFromCache(array $attributes = [])
    {

        $queryHash = $this->generateQueryHashFromQueryBuilder($attributes);

        if ($this->cache->has($queryHash)) {

            return $this->cache->get($queryHash);

        }

        return null;

    }

    /**
     * @param $cacheHashKeyPrefix
     * @return $this
     */
    public function setCacheHashKeyPrefix($cacheHashKeyPrefix)
    {

        $this->cacheHashKeyPrefix = $cacheHashKeyPrefix;

        return $this;

    }

    /**
     * @param $cacheLifeTime
     * @return $this
     */
    public function setCacheLifeTime($cacheLifeTime)
    {

        $this->cacheLifeTime = $cacheLifeTime;

        return $this;

    }

    /**
     * @param $result
     * @param array $attributes
     */
    public function storeResultInCache($result, array $attributes)
    {

        $queryHash = $this->generateQueryHashFromQueryBuilder($attributes);

        $this->cache->put($queryHash, $result, $this->cacheLifeTime);

    }

}
