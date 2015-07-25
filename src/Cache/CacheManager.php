<?php

namespace Algatux\Repository\Cache;

use Illuminate\Cache\Repository as CacheRepository;
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

    /**
     * @param CacheRepository $cacheRepository
     */
    public function __construct(CacheRepository $cacheRepository)
    {

        $this->cache = $cacheRepository;

    }

    /**
     * @param Builder $qb
     * @return string
     */
    private function generateQueryHashFromQueryBuilder(Builder $qb)
    {

        return sha1(
            implode('_', [
                $this->cacheHashKeyPrefix,
                $qb->toSql(),
                serialize($qb->getBindings())
            ])
        );

    }

    /**
     * @param Builder $qb
     * @return Collection|null
     */
    public function tryFetchResultFromCache(Builder $qb)
    {

        $queryHash = $this->generateQueryHashFromQueryBuilder($qb);

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
     * @param $qb
     * @param $result
     */
    public function storeResultInCache($qb, $result)
    {

        $queryHash = $this->generateQueryHashFromQueryBuilder($qb);
        $this->cache->put($queryHash, $result, $this->cacheLifeTime);

    }

}
