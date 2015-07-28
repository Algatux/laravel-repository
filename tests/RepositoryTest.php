<?php

namespace Algatux\Repository\Tests;

use Algatux\Repository\Eloquent\AbstractRepository;
use Algatux\Repository\Managers\ModelManager;
use Algatux\Repository\Tests\Fakes\FakeCriteriaOne;
use Algatux\Repository\Tests\Fakes\FakeModel;
use Algatux\Repository\Tests\Fakes\FakeRepository;
use Illuminate\Database\Eloquent\Model;

class RepositoryTest extends BaseTestCase
{

    public function test_repo_initializes_itself()
    {

        /** @var FakeRepository $repo */
        $repo = new FakeRepository($this->getModelManager()->reveal(), $this->getQueryManager()->reveal());

        $this->assertInstanceOf(AbstractRepository::class, $repo);

    }

    public function test_repository_exposes_a_valid_model_instance()
    {

        $modelManager = new ModelManager();
        $modelManager->setModelClassName(FakeModel::class);
        /** @var FakeRepository $repo */
        $repo = new FakeRepository($modelManager, $this->getQueryManager()->reveal());

        $this->assertInstanceOf(Model::class, $repo->expose());
        $this->assertInstanceOf(FakeModel::class, $repo->expose());

    }

    public function test_criteria_will_be_applied_to_model()
    {

        $model = new FakeModel;

        $modelManager = new ModelManager();
        $modelManager->setModelClassName(FakeModel::class);

        /** @var FakeRepository $repo */
        $repo = new FakeRepository($modelManager, $this->getQueryManager()->reveal());

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

}
