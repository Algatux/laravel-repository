<?php

namespace Algatux\Repository\Tests\Fakes;

use Algatux\Repository\Eloquent\AbstractRepository;

class FakeRepository extends AbstractRepository
{

    private $mcn;

    public function setModelClassName($name)
    {
        $this->mcn = $name;
    }

    public function resetModelClassName()
    {
        $this->mcn = null;
    }

    public function modelClassName()
    {
        if ($this->mcn)
            return $this->mcn;

        return FakeModel::class;
    }

}
