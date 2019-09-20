<?php


namespace SAREhub\DockerUtil\Worker;


use Psr\Log\LogLevel;
use SAREhub\Commons\Logger\StreamLoggerFactoryProvider;
use SAREhub\Commons\Misc\EnvironmentHelper;

abstract class SelectableWorkerBootstrap
{
    const ENV_WORKER_TYPE = "WORKER_TYPE";

    public function run(): void
    {
        $workerType = strtoupper(EnvironmentHelper::getRequiredVar(self::ENV_WORKER_TYPE));
        $factoryClass = $this->getWorkerContainerFactoryClassByType($workerType);
        $errorHandler = $this->createUnexpectedErrorHandler();
        WorkerBootstrap::create($factoryClass, $errorHandler)->run();
    }

    protected abstract function getWorkerContainerFactoryClassByType(string $type): string;

    protected function createUnexpectedErrorHandler(): UnexpectedErrorHandler
    {
        $defaultLoggerFactory = (new StreamLoggerFactoryProvider(LogLevel::CRITICAL, null))->get();
        return new DefaultUnexpectedErrorHandler($defaultLoggerFactory);
    }
}
