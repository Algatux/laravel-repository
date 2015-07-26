<?php

namespace Algatux\Repository\Test;

use Algatux\Repository\Eloquent\AbstractQueryCriteria;
use Algatux\Repository\Tests\Fakes\FakeCriteriaOne;
use Algatux\Repository\Tests\Fakes\FakeModel;
use Algatux\Repository\Tests\Fakes\FakeRepository;
use Algatux\Repository\Cache\CacheManager;
use Illuminate\Cache\Repository;
use Illuminate\Database\Eloquent\Model;
use Prophecy\Argument;

require __DIR__ . '/../../vendor/autoload.php';

class TestCriteria extends \PHPUnit_Framework_TestCase
{

    public function test_criteria_initializes_itself()
    {

        $criteria = new FakeCriteriaOne;
        $this->assertInstanceOf(AbstractQueryCriteria::class, $criteria);

    }

    public function test_that_criteria_will_apply_her_criteria_to_the_model()
    {
        $fakeModel = $this->prophesize(FakeModel::class);

        $fakeModel->where('field','=','1')->shouldBeCalledTimes(1);

        $criteria = new FakeCriteriaOne;

        $criteria->apply($fakeModel->reveal());

    }


}
