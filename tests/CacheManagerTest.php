<?php

namespace Algatux\Repository\Tests;

use Algatux\Repository\Managers\CacheManager;
use Illuminate\Cache\Repository;
use Prophecy\Argument;

class CacheManagerTest extends BaseTestCase
{

    public function test_initializes_itself()
    {

        $repoCache = $this->prophesize(Repository::class)->reveal();
        $cm = new CacheManager($repoCache);

        $this->assertInstanceOf(CacheManager::class, $cm);

    }

    public function test_manager_store_in_cache()
    {
        $repoCache = $this->prophesize(Repository::class);
        $repoCache->put(Argument::type('string'), Argument::type('array'), Argument::type('int'))->shouldBeCalled();

        $cm = new CacheManager($repoCache->reveal());

        $cm->storeResultInCache([], [1, 2, 3, 4, 5, 6]);
    }

    public function test_manager_get_from_cache_when_has_key()
    {
        $repoCache = $this->prophesize(Repository::class);

        $repoCache->has(Argument::type('string'))->shouldBeCalled();
        $repoCache->has(Argument::type('string'))->willReturn(true);

        $repoCache->get(Argument::type('string'))->shouldBeCalled();

        $cm = new CacheManager($repoCache->reveal());

        $cm->tryFetchResultFromCache([1, 2, 3, 4, 5, 6]);
    }

    public function test_manager_get_from_cache_when_hasnt_key()
    {
        $repoCache = $this->prophesize(Repository::class);

        $repoCache->has(Argument::type('string'))->shouldBeCalled();
        $repoCache->has(Argument::type('string'))->willReturn(false);

        $repoCache->get(Argument::type('string'))->shouldNotBeCalled();

        $cm = new CacheManager($repoCache->reveal());

        $cm->tryFetchResultFromCache([1, 2, 3, 4, 5, 6]);
    }

}
