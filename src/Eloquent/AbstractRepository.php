<?php

namespace Algatux\Repository\Eloquent;

use Algatux\Repository\Contracts\RepositoryInterface;

use Algatux\Repository\Exceptions\CriteriaNameNotStringException;
use Algatux\Repository\Exceptions\CriteriaScopeModelException;
use Algatux\Repository\Exceptions\ModelInstanceException;

use Algatux\Repository\Managers\ModelManager;
use Algatux\Repository\Managers\QueryManager;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AbstractRepository
 * @package Algatux\Repository\Eloquent
 */
abstract class AbstractRepository implements RepositoryInterface
{

    const HASH_KEY_PREFIX = 'algatux_laravel_repository';

    /** @var ModelManager */
    protected $modelManager;

    /** @var QueryManager */
    protected $queryManager;

    /**
     * @param ModelManager $modelManager
     * @param QueryManager $queryManager
     */
    public function __construct(ModelManager $modelManager, QueryManager $queryManager)
    {
        $this->modelManager = $modelManager;
        $this->queryManager = $queryManager;

        $this->modelManager->setModelClassName($this->modelClassName());
    }

    /**
     * Must return model full qualified class name
     *
     * @return string
     */
    abstract protected function modelClassName();

    /**
     * Exposes Eloquent Model
     *
     * @return Model
     */
    public function expose()
    {
        return $this->modelManager->getModelCopy();
    }

    /**
     * @param array $criteriaList
     * @return Model $model
     * @throws ModelInstanceException
     */
    public function filterByCriteria(array $criteriaList = [])
    {

        $model = $this->modelManager->getModelCopy();

        /** @var AbstractQueryCriteria $criteria */
        foreach ($criteriaList as $criteria) {

            $this->validateCriteria($criteria);

            $model = $criteria->apply($model);

        }

        return $model;

    }

    /**
     * @param AbstractQueryCriteria $criteria
     * @throws CriteriaScopeModelException
     * @throws CriteriaNameNotStringException
     */
    protected function validateCriteria(AbstractQueryCriteria $criteria)
    {

        if (!$criteria instanceof AbstractQueryCriteria) {
            throw new \InvalidArgumentException('Arument passed is not a criteria');
        }

        if ($criteria->modelScopeClass() !== $this->modelClassName()) {
            throw new CriteriaScopeModelException(get_class($criteria));
        }

        if (!is_string($criteria->criteriaName())) {
            throw new CriteriaNameNotStringException(get_class($criteria));
        }

    }





}
