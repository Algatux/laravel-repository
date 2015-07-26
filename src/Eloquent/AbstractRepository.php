<?php

namespace Algatux\Repository\Eloquent;

use Algatux\Repository\Contracts\RepositoryInterface;
use Algatux\Repository\Cache\CacheManager;

use Algatux\Repository\Exceptions\CriteriaNameNotStringException;
use Algatux\Repository\Exceptions\CriteriaScopeModelException;
use Algatux\Repository\Exceptions\ModelInstanceException;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * Class AbstractRepository
 * @package Algatux\Repository\Eloquent
 */
abstract class AbstractRepository implements RepositoryInterface
{

    /** @var Model */
    protected $model;

    /** @var CacheManager  */
    protected $cacheManager;

    /** @var bool */
    private $modelHasCriteria;

    /** @var  string */
    private $cacheHashKeyPrefix;

    /** @var bool */
    private $useResultCache;

    /**
     * @param CacheManager $cacheManager
     * @throws ModelInstanceException
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
        $this->cacheManager->setCacheHashKeyPrefix('algatux_laravel_repository');

        $this->initModel();
    }

    /**
     * @throws ModelInstanceException
     */
    private function initModel()
    {

        if ($this->modelHasCriteria or !$this->model) {

            $modelClassName = $this->modelClassName();

            /** @var Model $model */
            $model = new $modelClassName;

            if (!$model instanceof Model) {
                throw new ModelInstanceException($modelClassName);
            }

            $this->model = $model;

        }

        $this->cacheManager->setCacheLifeTime(1);

        $this->useResultCache = false;
        $this->modelHasCriteria = false;

    }

    /**
     * Must return model full qualified class name
     *
     * @return string
     */
    abstract protected function modelClassName();

    /**
     * @param array $criteriaList
     * @return Model $model
     * @throws ModelInstanceException
     */
    public function filterByCriteria(array $criteriaList = [])
    {

        $this->reset();

        $model = clone $this->model;

        /** @var AbstractQueryCriteria $criteria */
        foreach ($criteriaList as $criteria) {

            $this->validateCriteria($criteria);

            $model = $criteria->apply($model);


        }

        return $model;

    }

    /**
     * Resets the model instance
     *
     * @throws ModelInstanceException
     */
    protected function reset()
    {
        $this->initModel();
    }

    /**
     * @param AbstractQueryCriteria $criteria
     * @throws CriteriaScopeModelException
     * @throws CriteriaNameNotStringException
     */
    protected function validateCriteria(AbstractQueryCriteria $criteria)
    {

        if (!$criteria instanceof AbstractQueryCriteria) {
            throw new \InvalidArgumentException('Arument passed is not a criteria');
        }

        if ($criteria->modelScopeClass() !== $this->modelClassName()) {
            throw new CriteriaScopeModelException(get_class($criteria));
        }

        if (!is_string($criteria->criteriaName())) {
            throw new CriteriaNameNotStringException(get_class($criteria));
        }

    }

    /**
     * Exposes Eloquent Model
     *
     * @param bool $initModel
     * @return Model
     */
    public function expose($initModel = false)
    {

        if ($initModel) {

            $this->reset();

        }

        return $this->model;

    }

    /**
     * @param string $cacheHashKeyPrefix
     */
    public function setCacheHashKeyPrefix($cacheHashKeyPrefix)
    {
        $this->cacheHashKeyPrefix = $cacheHashKeyPrefix;
    }

    /**
     * @param int $minutesLifeTime
     * @return $this
     */
    protected function useCacheResult($minutesLifeTime = 1)
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
    protected function getResults(Builder $qb, $columns = ['*'])
    {

        $this->checkColumnsField($columns);

        $result = $this->fetchFromCache($qb);

        if (is_null($result)) {
            $result = $qb->get($columns);
        }

        $this->resultCacheStore($result);

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
    public function getPaginatedResult(Builder $qb, $pages=10, $columns = ['*'], $pageStringName = 'page')
    {

        $this->checkColumnsField($columns);

        $result = $this->fetchFromCache($qb,['pageRes'=>$pages,'columns'=>$columns,'pageString'=>$pageStringName]);

        if (is_null($result)) {
            $result = $qb->paginate($pages, $columns, $pageStringName);
        }

        $this->resultCacheStore($result);

        $this->reset();

        return $result;

    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    protected function getDefaultQueryBuilder()
    {
        return $this->model->query()->getQuery();
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
     * @param Builder $qb
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    private function fetchFromCache(Builder $qb, array $attributes=[])
    {
        if ($this->useResultCache) {
            return $this->cacheManager->tryFetchResultFromCache($qb,$attributes);
        }

        return null;
    }

    /**
     * @param $result
     */
    protected function resultCacheStore($result)
    {
        if ($this->useResultCache) {
            $this->cacheManager->storeResultInCache($result);
        }
    }

}
