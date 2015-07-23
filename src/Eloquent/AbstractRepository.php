<?php

namespace Algatux\Repository\Eloquent;

use Algatux\Repository\Contracts\RepositoryInterface;
use Algatux\Repository\Exceptions\ModelInstanceException;
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

    /** @var Model */
    protected $model;

    /** @var bool */
    protected $modelHasCriteria;

    /**
     * @throws ModelInstanceException
     */
    public function __construct()
    {
        $this->modelHasCriteria = false;
        $this->initModel();
    }

    /**
     * @param array $criteriaList
     * @return $this
     * @throws ModelInstanceException
     */
    public function filterByCriteria(array $criteriaList = [])
    {

        $this->initModel();

        /** @var AbstractQueryCriteria $criteria */
        foreach ($criteriaList as $criteria) {

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

        $this->initModel();

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

            $this->initModel();

        }

        return $this->model;

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

        $this->modelHasCriteria = false;

    }

    /**
     * Must return model full qualified class name
     *
     * @return string
     */
    abstract protected function modelClassName();

}
