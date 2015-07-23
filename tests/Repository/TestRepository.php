<?php

namespace Algatux\Repository\Test;

use Algatux\Repository\Tests\Fakes\FakeCriteriaOne;
use Algatux\Repository\Tests\Fakes\FakeModel;
use Algatux\Repository\Tests\Fakes\FakeRepository;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;

require __DIR__ . '/../../vendor/autoload.php';

class TestRepository extends \PHPUnit_Framework_TestCase
{

    public function test_repo_initializes_itself()
    {

        $app = $this->setupApplication();
        $repo = $app->make(FakeRepository::class);

        $this->assertInstanceOf(FakeRepository::class, $repo);

    }

    public function test_repository_exposes_a_valid_model_instance()
    {

        $app = $this->setupApplication();
        $repo = $app->make(FakeRepository::class);

        $this->assertInstanceOf(Model::class, $repo->expose());
        $this->assertInstanceOf(FakeModel::class, $repo->expose());

    }

    public function test_criteria_will_be_applied_to_model()
    {

        $app = $this->setupApplication();

        $model = $app->make(FakeModel::class);

        /** @var FakeRepository $repo */
        $repo = $app->make(FakeRepository::class);

        $criteria = $this->prophesize(FakeCriteriaOne::class);

        $criteria->apply($model)->shouldBeCalled();
        $criteria->apply($model)->willReturn(null);
        $criteria->criteriaName()->shouldBeCalled();
        $criteria->criteriaName()->willReturn();

        $repo->filterByCriteria([$criteria->reveal()]);

    }

    /**
     * @return Application
     */
    private function setupApplication()
    {

        // Create the application such that the config is loaded.
        $container = new Container();

        return $container;

    }

}
