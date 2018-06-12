<?php


namespace SAREhub\DockerUtil\Worker;


use SAREhub\Commons\Service\ServiceManager;
use SAREhub\Commons\Task\Task;

class StandardWorker extends BasicWorker
{
    /**
     * @var Task
     */
    private $initTask;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function __construct(Task $initTask, ServiceManager $serviceManager)
    {
        $this->initTask = $initTask;
        $this->serviceManager = $serviceManager;
    }

    protected function doStart()
    {
        $this->initTask->run();
        $this->getServiceManager()->start();
    }

    protected function doTick()
    {
        $this->getServiceManager()->tick();
    }

    protected function doStop()
    {
        $this->getServiceManager()->stop();
    }

    private function getServiceManager(): ServiceManager
    {
        return $this->serviceManager;
    }

}