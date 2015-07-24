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

    /** @var bool */
    protected $useResultCache;

    /** @var int */
    protected $resultCacheLifeTime;

    /** @var CacheRepository  */
    protected $cacheRepository;

    /**
     * @param CacheRepository $cacheRepository
     * @throws ModelInstanceException
     */
    public function __construct(CacheRepository $cacheRepository)
    {
        $this->cacheRepository = $cacheRepository;

        $this->initModel();
    }

    /**
     * @param bool|true $use
     * @param int $minutesLifeTime
     * @return $this
     */
    protected function useCacheResult($use=true, $minutesLifeTime=1)
    {
        $this->useResultCache = $use;
        $this->resultCacheLifeTime = $minutesLifeTime;
        return $this;
    }

    /**
     * @param array $criteriaList
     * @return $this
     * @throws ModelInstanceException
     */
    public function filterByCriteria(array $criteriaList = [])
    {

        $this->reset();

        /** @var AbstractQueryCriteria $criteria */
        foreach ($criteriaList as $criteria) {

            $this->validateCriteria($criteria);

            $this->model = $criteria->apply($this->model);

        }

        $this->modelHasCriteria = true;

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

        $queryHash = $this->generateQueryHash($qb);

        $queryResult = $this->fetchQueryFromCache($queryHash);

        if (is_null($queryResult)) {

            $queryResult = $qb->get($columns);

            $this->cacheRepository->put($queryHash, $queryResult, $this->resultCacheLifeTime);

        }

        $this->reset();

        return $queryResult;

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
     * Resets the model instance
     *
     * @throws ModelInstanceException
     */
    public function reset()
    {
        $this->initModel();
    }

    /**
     * @throws ModelInstanceException
     */
    protected function initModel()
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
     * @return \Illuminate\Database\Query\Builder|static
     */
    protected function getDefaultQueryBuilder()
    {
        return $this->model->query()->getQuery();
    }

    /**
     * @param Builder $qb
     * @return string
     */
    protected function generateQueryHash(Builder $qb)
    {
        return sha1($qb->toSql());
    }

    /**
     * Must return model full qualified class name
     *
     * @return string
     */
    abstract protected function modelClassName();

    /**
     * @param $criteria
     * @throws CriteriaNameNotStringException
     */
    public function validateCriteria(AbstractQueryCriteria $criteria)
    {

        if (!$criteria instanceof AbstractQueryCriteria) {
            throw new \InvalidArgumentException('Arument passed is not an array of only criterias');
        }

        if (is_string($criteria->criteriaName())) {
            throw new CriteriaNameNotStringException(get_class($criteria));

        }

    }

    /**
     * @param string $queryHash
     * @return Collection|Model|null
     */
    protected function fetchQueryFromCache($queryHash)
    {

        if ($this->useResultCache) {

            if ($this->cacheRepository->has($queryHash)) {

                return $this->cacheRepository->get($queryHash);

            }

        }

        return null;

    }

}
