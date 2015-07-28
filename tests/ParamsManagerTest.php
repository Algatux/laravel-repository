<?php

namespace Algatux\Repository\Tests;

use Algatux\Repository\Managers\ParamsManager;
use Illuminate\Http\Request;
use Prophecy\Argument;

class ParamsManagerTest extends BaseTestCase
{

    public function test_initializes_itself()
    {

        $request = $this->prophesize(Request::class)->reveal();
        $pm = new ParamsManager($request);

        $this->assertInstanceOf(ParamsManager::class, $pm);

    }

    public function test_set_param_name_throws_exception()
    {

        $request = $this->prophesize(Request::class)->reveal();
        $pm = new ParamsManager($request);

        $this->setExpectedException('\InvalidArgumentException');

        $pm->setPaginationParamString('');

    }

}
