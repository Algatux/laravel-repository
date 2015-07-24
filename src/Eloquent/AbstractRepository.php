<?php

namespace Algatux\Repository\Eloquent;

use Algatux\Repository\Contracts\RepositoryInterface;
use Algatux\Repository\Exceptions\CriteriaNameNotStringException;
use Algatux\Repository\Exceptions\ModelInstanceException;
use Illuminate\Cache\Repository as CacheRepository;
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

    /** @var bool */
    protected $modelHasCriteria;

    /** @var  string */
    protected $cacheHashKeyPrefix;

    /** @var bool */
    protected $useResultCache;

    /** @var int */
    protected $resultCacheLifeTime;

    /** @var CacheRepository */
    protected $cacheRepository;

    /**
     * @param CacheRepository $cacheRepository
     * @throws ModelInstanceException
     */
    public function __construct(CacheRepository $cacheRepository)
    {
        $this->cacheRepository = $cacheRepository;

        $this->cacheHashKeyPrefix = 'algatux_laravel_repository';
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

        $this->useResultCache = false;
        $this->resultCacheLifeTime = 1;
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
    public function reset()
    {
        $this->initModel();
    }

    /**
     * @param $criteria
     * @throws CriteriaNameNotStringException
     */
    private function validateCriteria(AbstractQueryCriteria $criteria)
    {

        if (!$criteria instanceof AbstractQueryCriteria) {
            throw new \InvalidArgumentException('Arument passed is not an array of only criterias');
        }

        if (is_string($criteria->criteriaName())) {
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
     * @param bool|true $use
     * @param int $minutesLifeTime
     * @return $this
     */
    protected function useCacheResult($use = true, $minutesLifeTime = 1)
    {
        $this->useResultCache = $use;
        $this->resultCacheLifeTime = $minutesLifeTime;
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

        if (!is_array($columns)) {
            throw new \InvalidArgumentException('Columns parameter given is not an array');
        }

        $queryHash = null;
        $queryResult = null;

        if ($this->useResultCache) {

            $queryHash = $this->generateQueryHash($qb);
            $queryResult = $this->fetchQueryFromCache($queryHash);

        }

        if (is_null($queryResult)) {

            $queryResult = $qb->get($columns);

        }

        if ($this->useResultCache) {

            $this->cacheRepository->put($queryHash, $queryResult, $this->resultCacheLifeTime);

        }

        $this->reset();

        return $queryResult;

    }

    /**
     * @param Builder $qb
     * @return string
     */
    private function generateQueryHash(Builder $qb)
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
     * @param string $queryHash
     * @return Collection|Model|null
     */
    private function fetchQueryFromCache($queryHash)
    {

        if ($this->useResultCache) {

            if ($this->cacheRepository->has($queryHash)) {

                return $this->cacheRepository->get($queryHash);

            }

        }

        return null;

    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    protected function getDefaultQueryBuilder()
    {
        return $this->model->query()->getQuery();
    }

}
