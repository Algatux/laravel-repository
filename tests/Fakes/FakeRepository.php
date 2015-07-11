<?php

namespace Algatux\Repository\Tests\Fakes;

use Algatux\Repository\Eloquent\AbstractRepository;

class FakeRepository extends AbstractRepository
{

    public function modelClassName()
    {
        return FakeModel::class;
    }

}