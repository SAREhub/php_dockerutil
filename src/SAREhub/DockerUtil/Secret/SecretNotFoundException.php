<?php

namespace SAREhub\DockerUtil\Secret;


use Throwable;

class SecretNotFoundException extends \RuntimeException
{
    public function __construct($secretName, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Secret '$secretName' not found", $code, $previous);
    }
}