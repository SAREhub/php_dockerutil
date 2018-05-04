<?php


namespace SAREhub\DockerUtil\Worker;


use Psr\Container\ContainerInterface;

interface UnexpectedErrorHandler
{
    public function handle(\Throwable $e, ?ContainerInterface $container = null);
}