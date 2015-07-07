<?php 

namespace Algatux\Repository\Eloquent;

use Algatux\Repository\Contracts\RepositoryInterface;
use Algatux\Repository\Exceptions\ModelInstanceException;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->model = $this->initModel();
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Model
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

        return $model;

    }

    /**
     * Must return model full qualified class name
     *
     * @return string
     */
    abstract protected function modelClassName();

}
