<?php

namespace Algatux\Repository\Managers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

/**
 * Class EloquentManager
 * @package Algatux\Repository\Managers
 */
class QueryManager
{

    /** @var CacheManager */
    public $cacheManager;

    /** @var ParamsManager */
    public $paramsManager;

    /** @var bool */
    private $useResultCache;

    /**
     * @param CacheManager $cacheManager
     * @param ParamsManager $paramsManager
     */
    public function __construct(CacheManager $cacheManager, ParamsManager $paramsManager)
    {
        $this->cacheManager = $cacheManager;
        $this->paramsManager = $paramsManager;

    }

    /**
     * @param int $minutesLifeTime
     * @return $this
     */
    public function useCacheResult($minutesLifeTime = CacheManager::DEFAULT_CACHE_LIFETIME)
    {
        $this->useResultCache = true;
        $this->cacheManager->setCacheLifeTime($minutesLifeTime);
        return $this;
    }

    /**
     * Gets results based on actual conditions, will use cached result if previously specified
     *
     * @param Builder $qb
     * @param array $columns
     * @return Collection
     */
    public function getResults(Builder $qb, $columns = ['*'])
    {

        $this->checkColumnsField($columns);

        $result = $this->fetchFromCache(func_get_args());

        if (is_null($result)) {
            $result = $qb->get($columns);
            $this->resultCacheStore($result);
        }

        $this->reset();

        return $result;

    }

    /**
     * @param Builder $qb
     * @param int $pages
     * @param array $columns
     * @param string $pageStringName
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|null
     */
    public function getPaginatedResult(Builder $qb, $pages = 10, $columns = ['*'], $pageStringName = 'page')
    {

        $this->checkColumnsField($columns);

        $result = $this->fetchFromCache(func_get_args());

        if (is_null($result)) {
            $result = $qb->paginate($pages, $columns, $pageStringName);
            $this->resultCacheStore($result);
        }

        $this->reset();

        return $result;

    }

    /**
     * @param $columns
     */
    private function checkColumnsField($columns)
    {
        if (!is_array($columns)) {
            throw new \InvalidArgumentException('Columns parameter given is not an array');
        }
    }

    /**
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    private function fetchFromCache(array $attributes = [])
    {

        if ($this->useResultCache) {

            $attributeList = $this->parseQueryAttributes($attributes);

            return $this->cacheManager->tryFetchResultFromCache($attributeList);
        }

        return null;
    }

    /**
     * @param $result
     * @param array $attributes
     */
    private function resultCacheStore($result, array $attributes = [])
    {
        if ($this->useResultCache) {

            $attributeList = $this->parseQueryAttributes($attributes);

            $this->cacheManager->storeResultInCache($result, $attributeList);
        }
    }


    private function reset()
    {
        $this->useResultCache = false;
    }

    /**
     * @param array $attributes
     * @return array
     */
    private function parseQueryAttributes(array $attributes)
    {
        $attributeList = [];

        foreach ($attributes as $index => $attribute) {

            if ($attribute instanceof Builder) {
                $attributeList[$index] = $attribute->toSql();
                continue;
            }

            $attributeList[$index] = $attribute;

        }

        if ($this->paramsManager->getPaginationParams()) {
            $attributeList[count($attributeList)] = $this->paramsManager->getPaginationParams();
            return $attributeList;
        }
        return $attributeList;
    }

}
