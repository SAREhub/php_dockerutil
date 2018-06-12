<?php

use SAREhub\Commons\Logger\DefaultJsonLogFormatter;
use SAREhub\Commons\Logger\StreamLoggerFactoryProvider;
use SAREhub\Commons\Process\PcntlSignals;
use SAREhub\Commons\Service\ServiceManager;
use SAREhub\Commons\Service\ServiceSupport;
use SAREhub\Commons\Task\Task;
use SAREhub\DockerUtil\Worker\StandardWorker;
use SAREhub\DockerUtil\Worker\WorkerRunner;

require dirname(__DIR__) . "/bootstrap.php";

class ExampleInitTask implements Task
{
    public function run()
    {
        printLine("INIT_TASK: initiating worker...");
        sleep(1);
        printLine("INIT_TASK: worker initiated");
    }
}

class ExampleService extends ServiceSupport
{
    /**
     * @var PcntlSignals
     */
    private $pcntl;

    private $tickCounter = 0;


    public function __construct(PcntlSignals $pcntl)
    {
        $this->pcntl = $pcntl;
    }

    protected function doStart()
    {
        sleep(1);
    }

    protected function doTick()
    {
        printLine("SERVICE: hard working...");
        sleep(2);
        $this->tickCounter++;
        if ($this->tickCounter > 2) {
            printLine("sending SIGTERM...");
            // pcntl signals are not supported on Windows platform but we can send fake signal to show stop worker logic
            $this->pcntl->dispatchSignal(PcntlSignals::SIGTERM);
        }
    }

    protected function doStop()
    {
        sleep(1);
    }
}

$loggerFactory = (new StreamLoggerFactoryProvider("DEBUG", new DefaultJsonLogFormatter()))->get();
$pcntl = new PcntlSignals();
$service = new ExampleService($pcntl);
$service->setLogger($loggerFactory->create("Example Service"));
$worker = new StandardWorker(new ExampleInitTask(), new ServiceManager([$service]));
$worker->setLogger($loggerFactory->create("Worker"));
$workerRunner = new WorkerRunner($worker, $pcntl);

$workerRunner->run();