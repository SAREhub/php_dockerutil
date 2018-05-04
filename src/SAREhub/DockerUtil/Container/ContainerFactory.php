<?php


namespace SAREhub\DockerUtil\Container;


use Psr\Container\ContainerInterface;

interface ContainerFactory
{
    public function create(): ContainerInterface;
}