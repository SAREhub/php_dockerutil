<?php


namespace SAREhub\DockerUtil\Worker;


use Psr\Log\LogLevel;
use SAREhub\Commons\Logger\StreamLoggerFactoryProvider;
use SAREhub\Commons\Misc\EnvironmentHelper;
use SAREhub\Commons\Misc\ErrorHandlerHelper;

abstract class SelectableWorkerBootstrap
{
    const ENV_WORKER_TYPE = "WORKER_TYPE";

    public function run(): void
    {
        $unexpectedErrorHandler = $this->createUnexpectedErrorHandler();
        $this->registerErrorHandler();
        try {
            $workerType = $this->getWorkerType();
            $factoryClass = $this->getWorkerContainerFactoryClassByType($workerType);
            WorkerBootstrap::create($factoryClass, $unexpectedErrorHandler)->run();
        } catch (\Throwable $e) {
            $unexpectedErrorHandler->handle($e);
        }
    }

    protected function registerErrorHandler(): void
    {
        ErrorHandlerHelper::registerDefaultErrorHandler(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    }

    protected function getWorkerType(): string
    {
        return strtoupper(EnvironmentHelper::getRequiredVar(self::ENV_WORKER_TYPE));
    }

    protected abstract function getWorkerContainerFactoryClassByType(string $type): string;

    protected function createUnexpectedErrorHandler(): UnexpectedErrorHandler
    {
        $defaultLoggerFactory = (new StreamLoggerFactoryProvider(LogLevel::CRITICAL, null))->get();
        return new DefaultUnexpectedErrorHandler($defaultLoggerFactory);
    }
}
