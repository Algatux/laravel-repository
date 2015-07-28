<?php

namespace Algatux\Repository\Tests;

use Algatux\Repository\Managers\QueryManager;

class QueryManagerTest extends BaseTestCase
{

    public function test_initializes_itself()
    {

        $qm = new QueryManager($this->getCacheManager()->reveal(), $this->getParamsManager()->reveal());

        $this->assertInstanceOf(QueryManager::class, $qm);

    }

}
