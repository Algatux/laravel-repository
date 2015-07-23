<?php

namespace Algatux\Repository\Eloquent;

use Algatux\Repository\Contracts\RepositoryInterface;
use Algatux\Repository\Exceptions\CriteriaNameNotStringException;
use Algatux\Repository\Exceptions\ModelInstanceException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    /** @var string */
    protected $cacheResultName;

    /**
     * @throws ModelInstanceException
     */
    public function __construct()
    {
        $this->initModel();
    }

    public function useCacheResult($use=true, $minutes=10)
    {
        $this->useResultCache = $use;
        $this->resultCacheLifeTime = $minutes;
        return $this->model;
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
     * @param array $columns
     * @return Collection
     */
    public function get($columns = ['*'])
    {

        return $this->model->get($columns);

    }

    /**
     * @param $id
     * @param string $id_field
     * @return Model
     * @throws ModelNotFoundException
     */
    public function find($id, $id_field = 'id')
    {

        $this->reset();

        $result = $this->model->where($id_field, '=', $id)->first();

        if (empty($result)) {
            $e = new ModelNotFoundException();
            $e->setModel($this->modelClassName());
            throw $e;
        }

        return $result;

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
        $this->resultCacheLifeTime = 10;
        $this->cacheResultName = null;
        $this->modelHasCriteria = false;

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

}
