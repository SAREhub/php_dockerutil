<?php

namespace SAREhub\DockerUtil\Worker;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SAREhub\Commons\Service\ServiceManager;
use SAREhub\Commons\Task\Task;

class StandardWorkerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Task | MockInterface
     */
    private $initTask;

    /**
     * @var ServiceManager | MockInterface
     */
    private $serviceManager;

    /**
     * @var Worker
     */
    private $worker;

    protected function setUp()
    {
        $this->initTask = \Mockery::mock(Task::class);
        $this->serviceManager = \Mockery::mock(ServiceManager::class);

        $this->worker = new StandardWorker($this->initTask, $this->serviceManager);
    }

    public function testStart()
    {
        $this->initTask->expects("run");
        $this->serviceManager->expects("start");

        $this->worker->start();
    }

    public function testTick()
    {
        $this->initTask->allows("run");
        $this->serviceManager->allows("start");
        $this->worker->start();

        $this->serviceManager->expects("tick");

        $this->worker->tick();
    }

    public function testStop()
    {
        $this->initTask->allows("run");
        $this->serviceManager->allows("start");
        $this->worker->start();

        $this->serviceManager->expects("stop");

        $this->worker->stop();
    }
}
