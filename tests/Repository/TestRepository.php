<?php

namespace Algatux\Repository\Test;

use Algatux\Repository\Eloquent\AbstractRepository;
use Algatux\Repository\Tests\Fakes\FakeCriteriaOne;
use Algatux\Repository\Tests\Fakes\FakeModel;
use Algatux\Repository\Tests\Fakes\FakeRepository;
use Algatux\Repository\Cache\CacheManager;
use Illuminate\Cache\Repository;
use Illuminate\Database\Eloquent\Model;
use Prophecy\Argument;

require __DIR__ . '/../../vendor/autoload.php';

class TestRepository extends \PHPUnit_Framework_TestCase
{

    public function test_repo_initializes_itself()
    {

        $repo = new FakeRepository($this->getCacheManager());

        $this->assertInstanceOf(AbstractRepository::class, $repo);

    }

    public function test_repository_exposes_a_valid_model_instance()
    {

        $repo = new FakeRepository($this->getCacheManager());

        $this->assertInstanceOf(Model::class, $repo->expose());
        $this->assertInstanceOf(FakeModel::class, $repo->expose());

    }

    public function test_criteria_will_be_applied_to_model()
    {

        $model = new FakeModel;

        /** @var FakeRepository $repo */
        $repo = new FakeRepository($this->getCacheManager());

        $criteria = $this->prophesize(FakeCriteriaOne::class);

        $criteria->apply($model)->shouldBeCalledTimes(1);
        $criteria->apply($model)->willReturn($model);
        $criteria->modelScopeClass()->shouldBeCalledTimes(1);
        $criteria->modelScopeClass()->willReturn(FakeModel::class);
        $criteria->criteriaName()->shouldBeCalledTimes(1);
        $criteria->criteriaName()->willReturn('fake_ctriteria_name');

        $criteriaModel = $repo->filterByCriteria([$criteria->reveal()]);

        $this->assertInstanceOf(FakeModel::class,$criteriaModel);

    }

    /**
     * @return CacheManager
     */
    private function getCacheManager()
    {
        /** @var Repository $cache */
        $cache = $this->prophesize(Repository::class)->reveal();
        return new CacheManager($cache);
    }

}
