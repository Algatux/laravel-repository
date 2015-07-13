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

    /** @var Container */
    protected $container;

    /** @var Model */
    protected $model;

    /** @var bool */
    protected $modelHasCriteria;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->modelHasCriteria = false;
        $this->initModel();
    }

    /**
     * @param array $criteriaList
     * @return Collection|null
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
     */
    public function get($columns = ['*'])
    {

        $this->model->all($columns);

    }

    /**
     * @param $id
     * @param string $id_field
     * @return Model
     * @throws ModelNotFoundException
     */
    public function find($id, $id_field='id')
    {

        $this->initModel();

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
            $model = $this->container->make($modelClassName);

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
