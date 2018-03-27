<?php


namespace SAREhub\DockerUtil\Worker;

use Psr\Container\ContainerInterface;
use SAREhub\Commons\Logger\LoggerFactory;
use SAREhub\Commons\Process\PcntlSignals;

class WorkerBootstrap
{
    public static function runFor(string $workerClass, ContainerInterface $container)
    {
        $loggerFactory = $container->get(LoggerFactory::class);
        $logger = $loggerFactory->create("main");
        try {
            $worker = self::createWorker($workerClass, $container);
            $worker->setLogger($loggerFactory->create($workerClass));

            $runner = new WorkerRunner($worker, new PcntlSignals());
            $runner->run();
        } catch (\Throwable $e) {
            $logger->critical("Exception outside runner: " . $e->getMessage(), [
                "exception" => $e
            ]);

            exit(1);
        }
    }

    protected static function createWorker(string $workerClass, ContainerInterface $container): Worker
    {
        return new $workerClass($container);
    }
}