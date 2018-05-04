<?php

namespace SAREhub\DockerUtil\Worker;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use SAREhub\Commons\Logger\LoggerFactory;

class DefaultUnexpectedErrorHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var LoggerFactory | MockInterface
     */
    private $defaultLoggerFactory;

    /**
     * @var LoggerInterface | MockInterface
     */
    private $logger;

    protected function setUp()
    {
        $this->defaultLoggerFactory = \Mockery::mock(LoggerFactory::class);
        $this->logger = \Mockery::mock(LoggerInterface::class);
    }

    public function testHandleWhenContainerNotNullAndHasLoggerFactory()
    {
        $handler = new DefaultUnexpectedErrorHandler($this->defaultLoggerFactory);
        $e = new \Exception("test");
        $container = \Mockery::mock(ContainerInterface::class);
        $container->expects("has")->withArgs([LoggerFactory::class])->andReturn(true);
        $loggerFactory = \Mockery::mock(LoggerFactory::class);
        $container->expects("get")->withArgs([LoggerFactory::class])->andReturn($loggerFactory);

        $loggerFactory->expects("create")
            ->withArgs([DefaultUnexpectedErrorHandler::LOGGER_NAME])->andReturn($this->logger);
        $this->logger->expects("critical")->withArgs(["Unexpected error occur: test", ["exception" => $e]]);

        $handler->handle($e, $container);
    }

    public function testHandleWhenContainerNotNullAndHasNotLoggerFactory()
    {
        $handler = new DefaultUnexpectedErrorHandler($this->defaultLoggerFactory);
        $e = new \Exception("test");
        $container = \Mockery::mock(ContainerInterface::class);
        $container->expects("has")->withArgs([LoggerFactory::class])->andReturn(false);
        $this->defaultLoggerFactory->expects("create")
            ->withArgs([DefaultUnexpectedErrorHandler::LOGGER_NAME])->andReturn($this->logger);
        $this->logger->expects("critical")->withArgs(["Unexpected error occur: test", ["exception" => $e]]);

        $handler->handle($e, $container);
    }

    public function testHandleWhenContainerIsNull()
    {
        $handler = new DefaultUnexpectedErrorHandler($this->defaultLoggerFactory);
        $e = new \Exception("test");
        $this->defaultLoggerFactory->expects("create")
            ->withArgs([DefaultUnexpectedErrorHandler::LOGGER_NAME])->andReturn($this->logger);
        $this->logger->expects("critical")->withArgs(["Unexpected error occur: test", ["exception" => $e]]);

        $handler->handle($e);
    }
}
