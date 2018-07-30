<?php


namespace SAREhub\DockerUtil\Worker;

use Psr\Container\ContainerInterface;
use SAREhub\DockerUtil\Container\ContainerFactory;

class WorkerBootstrap
{
    const ERROR_EXIT_CODE = 1;

    /**
     * @var ContainerFactory | string
     */
    private $containerFactory;

    /**
     * @var UnexpectedErrorHandler
     */
    private $errorHandler;

    /**
     * @param ContainerFactory | string $containerFactory Object or class name
     * @param UnexpectedErrorHandler $errorHandler
     */
    public function __construct($containerFactory, UnexpectedErrorHandler $errorHandler)
    {
        $this->containerFactory = $containerFactory;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @param ContainerFactory | string $containerFactory Object or class name
     * @param UnexpectedErrorHandler $errorHandler
     * @return WorkerBootstrap
     */
    public static function create($containerFactory, UnexpectedErrorHandler $errorHandler): self
    {
        return new self($containerFactory, $errorHandler);
    }

    /**
     * Creates container and gets WorkerRunner then run
     * Handles unexpected errors
     */
    public function run()
    {
        try {
            $container = $this->createContainer();
            $runner = $container->get(WorkerRunner::class);
            $runner->run();
        } catch (\Throwable $e) {
            $this->handleUnexpectedError($e, $container ?? null);
        }
    }

    private function createContainer(): ContainerInterface
    {
        $containerFactory = $this->containerFactory;
        $containerFactory = (is_string($containerFactory)) ? new $containerFactory : $containerFactory;
        return $containerFactory->create();
    }

    private function handleUnexpectedError(\Throwable $e, ?ContainerInterface $container): void
    {
        $this->errorHandler->handle($e, $container);
        exit(self::ERROR_EXIT_CODE);
    }
}