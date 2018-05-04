<?php


namespace SAREhub\DockerUtil\Worker;

use Psr\Container\ContainerInterface;
use SAREhub\DockerUtil\Container\ContainerFactory;

class WorkerBootstrap
{
    const ERROR_EXIT_CODE = 1;

    /**
     * @var ContainerFactory
     */
    private $containerFactory;

    /**
     * @var UnexpectedErrorHandler
     */
    private $errorHandler;

    /**
     * @param ContainerFactory $containerFactory
     * @param UnexpectedErrorHandler $errorHandler
     */
    public function __construct(ContainerFactory $containerFactory, UnexpectedErrorHandler $errorHandler)
    {
        $this->containerFactory = $containerFactory;
        $this->errorHandler = $errorHandler;
    }

    public static function create(ContainerFactory $containerFactory, UnexpectedErrorHandler $errorHandler): self
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
            $container = $this->containerFactory->create();
            $runner = $container->get(WorkerRunner::class);
            $runner->run();
        } catch (\Throwable $e) {
            $this->handleUnexpectedError($e, $container ?? null);
        }
    }

    private function handleUnexpectedError(\Throwable $e, ?ContainerInterface $container): void
    {
        $this->errorHandler->handle($e, $container);
        exit(self::ERROR_EXIT_CODE);
    }


}