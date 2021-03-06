<?php

namespace Algatux\Repository\Eloquent;

use Algatux\Repository\Contracts\QueryCriteriaInterface;
use Algatux\Repository\Exceptions\ModelInstanceException;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AbstractQueryCriteria
 * @package Algatux\Repository\Eloquent
 */
abstract class AbstractQueryCriteria implements QueryCriteriaInterface
{

    /** @var  array */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @param Model $model
     * @return Model
     * @throws ModelInstanceException
     */
    public function apply(Model $model)
    {

        return $this->criteria($model);

    }

    /**
     * Must return model full qualified class name
     *
     * @return string
     */
    abstract public function modelScopeClass();

    /**
     * @param Model $model
     * @return mixed
     */
    abstract protected function criteria(Model $model);

    /**
     * Must Return an unique criteria name
     *
     * @return string
     */
    abstract public function criteriaName();

}
