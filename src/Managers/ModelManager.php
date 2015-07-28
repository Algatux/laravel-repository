<?php

namespace Algatux\Repository\Managers;

use Algatux\Repository\Exceptions\ModelClassNameException;
use Algatux\Repository\Exceptions\ModelInstanceException;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelManager
 * @package Algatux\Repository\Managers
 */
class ModelManager
{

    /** @var Model */
    private $model;

    /** @var string */
    private $modelClassName;

    /** @var bool */
    private $modelHasCriteria;

    /**
     * @param string $modelClassName
     * @throws ModelInstanceException
     */
    public function __construct($modelClassName = null)
    {
        if (!is_null($modelClassName)) {
            $this->modelClassName = $modelClassName;
            $this->initModel();
        }
    }

    public function setModelClassName($modelClassName)
    {
        self::__construct($modelClassName);
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
     * @return Model
     */
    public function getModelCopy()
    {
        return clone $this->model;
    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    protected function getQueryBuilder()
    {
        return $this->model->query()->getQuery();
    }

    /**
     * @throws ModelInstanceException
     */
    private function initModel()
    {

        if (!is_string($this->modelClassName) or !class_exists($this->modelClassName)) {
            throw new ModelClassNameException($this->modelClassName);
        }

        if ($this->modelHasCriteria or !$this->model) {

            /** @var Model $model */
            $model = new $this->modelClassName;

            if (!$model instanceof Model) {
                throw new ModelInstanceException($this->modelClassName);
            }

            $this->model = $model;

        }

        $this->modelHasCriteria = false;

    }

}
