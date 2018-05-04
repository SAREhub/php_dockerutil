<?php


namespace SAREhub\DockerUtil\Worker;


use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use SAREhub\Commons\Logger\LoggerFactory;

class DefaultUnexpectedErrorHandler implements UnexpectedErrorHandler
{
    const LOG_MESSAGE_FORMAT = "Unexpected error occur: %s";
    const LOGGER_NAME = "MAIN";

    /**
     * @var LoggerFactory
     */
    private $defaultLoggerFactory;

    /**
     * @param LoggerFactory $defaultLoggerFactory
     *        Will be used when container instance is null or LoggerFactory entry isn't exists.
     */
    public function __construct(LoggerFactory $defaultLoggerFactory)
    {
        $this->defaultLoggerFactory = $defaultLoggerFactory;
    }

    public function handle(\Throwable $e, ?ContainerInterface $container = null)
    {
        $this->getLogger($container)->critical(sprintf(self::LOG_MESSAGE_FORMAT, $e->getMessage()), [
            "exception" => $e
        ]);
    }

    private function getLogger(?ContainerInterface $container): LoggerInterface
    {
        return $this->getLoggerFactory($container)->create(self::LOGGER_NAME);
    }

    private function getLoggerFactory(?ContainerInterface $container): LoggerFactory
    {
        if ($container === null || !$container->has(LoggerFactory::class)) {
            return $this->defaultLoggerFactory;
        }
        return $container->get(LoggerFactory::class);
    }
}