<?php 

namespace Algatux\Repository\Eloquent;

use Algatux\Repository\Contracts\QueryCriteriaInterface;
use Algatux\Repository\Contracts\RepositoryInterface;
use Algatux\Repository\Exceptions\ModelInstanceException;
use Algatux\Repository\Exceptions\RepositoryException;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    /**
     * @param array $columns
     * @param bool|true $reset
     * @return Collection|null
     * @throws ModelInstanceException
     */
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
     * @param $id
     * @param string $id_field
     * @return Model
     * @throws ModelNotFoundException
     */
    public function find($id, $id_field='id')
    {
        $result = $this->model->where($id_field,'=',$id)->first();

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
