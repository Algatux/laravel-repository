<?php

namespace Algatux\Repository\Tests\Fakes;

use Algatux\Repository\Eloquent\AbstractQueryCriteria;
use Illuminate\Database\Eloquent\Model;

class FakeCriteriaOne extends AbstractQueryCriteria
{

    /**
     * Must return model full qualified class name
     *
     * @return string
     */
    protected function modelClass()
    {
        return FakeModel::class;
    }

    /**
     * @param Model $model
     * @return Model
     */
    protected function criteria(Model $model)
    {
        return $model->where('field','=','1');
    }

}
