<?php 

namespace Algatux\Repository\Eloquent;

use Algatux\Repository\Contracts\QueryCriteriaInterface;
use Algatux\Repository\Contracts\RepositoryInterface;
use Algatux\Repository\Exceptions\ModelInstanceException;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class AbstractRepository
 * @package Algatux\Repository\Eloquent
 */
abstract class AbstractRepository implements RepositoryInterface
{

    /** @var Container */
    protected $container;

    /** @var Model */
    protected $model;

    /** @var Collection[QueryCriteriaInterface] */
    protected $criteriaList;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->initModel();
        $this->clearCriteria();
    }

    public function filterByCriteria($columns = ['*'], $reset = true)
    {
        if ($reset) {
            $this->initModel();
        }
        /** @var AbstractQueryCriteria $criteria */
        foreach ($this->criteriaList as $criteria) {

            $this->model = $criteria->apply($this->model);

        }
        return $this->model->get($columns);
    }

    /**
     * Exposes Eloquent Model
     *
     * @return Model
     */
    public function expose()
    {
        return $this->model;
    }

    /**
     * @param QueryCriteriaInterface $criteria
     */
    public function addCriteria(QueryCriteriaInterface $criteria)
    {
        $this->criteriaList->push($criteria);
    }

    /**
     * Re-Inits a fresh new Model and criteria Collection
     */
    public function clearCriteria()
    {
        $this->initModel();
        $this->initCriteria();
    }

    /**
     * @throws ModelInstanceException
     */
    protected function initModel()
    {

        $modelClassName = $this->modelClassName();

        /** @var Model $model */
        $model = $this->container->make($modelClassName);

        if (!$model instanceof Model) {
            throw new ModelInstanceException($modelClassName);
        }

        $this->model = $model;

    }

    /**
     * Inits Criteria list
     */
    protected function initCriteria()
    {
        $this->criteriaList = new Collection();
    }

    /**
     * Must return model full qualified class name
     *
     * @return string
     */
    abstract protected function modelClassName();

}
