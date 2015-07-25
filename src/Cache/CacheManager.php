<?php

namespace Algatux\Repository\Cache;

use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

class CacheManager
{

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

    }

    /**
     * @param Builder $qb
     * @param array $attributes
     */
    private function generateQueryHashFromQueryBuilder(Builder $qb, array $attributes)
    {

        $this->actualQueryHash = sha1(
            implode('_', [
                $this->cacheHashKeyPrefix,
                $qb->toSql(),
                serialize($qb->getBindings()),
                serialize($attributes)
            ])
        );

    }

    /**
     * @param Builder $qb
     * @param array $attributes
     * @return LengthAwarePaginator|Collection|null
     */
    public function tryFetchResultFromCache(Builder $qb, array $attributes=[])
    {

        $this->generateQueryHashFromQueryBuilder($qb,$attributes);

        if ($this->cache->has($this->actualQueryHash)) {

            return $this->cache->get($this->actualQueryHash);

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
     */
    public function storeResultInCache($result)
    {

        $this->cache->put($this->actualQueryHash, $result, $this->cacheLifeTime);

    }

}
