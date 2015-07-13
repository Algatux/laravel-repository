<?php

namespace Algatux\Repository\Contracts;

use Illuminate\Database\Eloquent\Model;

interface QueryCriteriaInterface
{

    public function apply(Model $model);

}
