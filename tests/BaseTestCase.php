<?php

namespace Algatux\Repository\Tests;

use Algatux\Repository\Managers\CacheManager;
use Algatux\Repository\Managers\ModelManager;
use Algatux\Repository\Managers\ParamsManager;
use Algatux\Repository\Managers\QueryManager;
use Illuminate\Cache\Repository;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @return CacheManager
     */
    protected function getCacheManager()
    {
        return $this->prophesize(CacheManager::class);
    }

    /**
     * @return Repository
     */
    protected function getCacheRepository()
    {
        /** @var Repository $cache */
        return $this->prophesize(Repository::class);
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function getModelManager()
    {
        $manager = $this->prophesize(ModelManager::class);
        return $manager;
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function getQueryManager()
    {
        $manager = $this->prophesize(QueryManager::class);
        return $manager;
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function getParamsManager()
    {
        $manager = $this->prophesize(ParamsManager::class);
        return $manager;
    }

}
