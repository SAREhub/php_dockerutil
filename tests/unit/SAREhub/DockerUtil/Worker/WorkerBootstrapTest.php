<?php

namespace SAREhub\DockerUtil\Worker;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use SAREhub\DockerUtil\Container\ContainerFactory;

class WorkerBootstrapTest extends TestCase
{

    use MockeryPHPUnitIntegration;

    /**
     * @var ContainerInterface | MockInterface
     */
    private $container;

    /**
     * @var ContainerFactory | MockInterface
     */
    private $containerFactory;

    /**
     * @var UnexpectedErrorHandler | MockInterface
     */
    private $unexpectedErrorHandler;

    /**
     * @var WorkerBootstrap
     */
    private $bootstrap;

    protected function setUp()
    {
        $this->containerFactory = \Mockery::mock(ContainerFactory::class);
        $this->container = \Mockery::mock(ContainerInterface::class);
        $this->unexpectedErrorHandler = \Mockery::mock(UnexpectedErrorHandler::class);
        $this->bootstrap = new WorkerBootstrap($this->containerFactory, $this->unexpectedErrorHandler);
        $this->bootstrap->setUnexpectedErrorExitFunction(function () {
        });
    }

    public function testRunThenWorkerRunnerRun()
    {
        $this->containerFactory->expects("create")->andReturn($this->container);
        $runner = \Mockery::mock(WorkerRunner::class);
        $this->container->expects("get")->with(WorkerRunner::class)->andReturn($runner);
        $runner->expects("run");
        $this->bootstrap->run();
    }

    public function testRunWhenCreateContainerError()
    {
        $exception = new \Exception();
        $this->containerFactory->expects("create")->andThrow($exception);
        $this->unexpectedErrorHandler->expects("handle")->with($exception, null);
        $this->bootstrap->run();
    }

    public function testRunWhenWorkerRunnerRunError()
    {
        $this->containerFactory->expects("create")->andReturn($this->container);
        $runner = \Mockery::mock(WorkerRunner::class);
        $this->container->expects("get")->with(WorkerRunner::class)->andReturn($runner);

        $exception = new \Exception();
        $runner->expects("run")->andThrow($exception);
        $this->unexpectedErrorHandler->expects("handle")->with($exception, $this->container);
        $this->bootstrap->run();
    }
}
