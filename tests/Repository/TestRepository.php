<?php

namespace Algatux\Repository\Test;

use Algatux\Repository\Tests\Fakes\FakeModel;
use Algatux\Repository\Tests\Fakes\FakeRepository;
use Illuminate\Foundation\Application;

require __DIR__ . '/../../vendor/autoload.php';

class TestRepository extends \PHPUnit_Framework_TestCase
{

    public function test_repo_initializes_itself()
    {

        $repo = $this->prophesize(FakeRepository::class);

        $this->assertInstanceOf(FakeRepository::class, $repo->reveal());

    }

    public function test_repository_exposes_model_instance()
    {

        $app = $this->setupApplication();
        $repo = $app->make(FakeRepository::class);

        $this->assertInstanceOf(FakeModel::class,$repo->expose());

    }

    /**
    * @return Application
    */
    private function setupApplication()
    {
        // Create the application such that the config is loaded.
        $app = new Application();
        $app->setBasePath(__DIR__ . "/../../../");
        $app->instance(FakeModel::class, new FakeModel());
        return $app;
    }

}
