<?php

namespace Algatux\Repository\Test;

use Algatux\Repository\Tests\Fakes\FakeCriteriaOne;
use Algatux\Repository\Tests\Fakes\FakeModel;
use Algatux\Repository\Tests\Fakes\FakeRepository;
use Illuminate\Cache\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;

require __DIR__ . '/../../vendor/autoload.php';

class TestRepository extends \PHPUnit_Framework_TestCase
{

    public function test_repo_initializes_itself()
    {

        $repo = new FakeRepository($this->prophesize(Repository::class)->reveal());

        $this->assertInstanceOf(FakeRepository::class, $repo);

    }

    public function test_repository_exposes_a_valid_model_instance()
    {

        $repo = new FakeRepository($this->prophesize(Repository::class)->reveal());

        $this->assertInstanceOf(Model::class, $repo->expose());
        $this->assertInstanceOf(FakeModel::class, $repo->expose());

    }

    public function test_criteria_will_be_applied_to_model()
    {

        $model = new FakeModel;

        /** @var FakeRepository $repo */
        $repo = new FakeRepository($this->prophesize(Repository::class)->reveal());

        $criteria = $this->prophesize(FakeCriteriaOne::class);

        $criteria->apply($model)->shouldBeCalled();
        $criteria->apply($model)->willReturn(null);
        $criteria->criteriaName()->shouldBeCalled();
        $criteria->criteriaName()->willReturn();

        $repo->filterByCriteria([$criteria->reveal()]);

    }

}
