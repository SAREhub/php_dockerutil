<?php


namespace SAREhub\DockerUtil\Secret;


use SAREhub\Commons\Secret\SecretValueNotFoundException;
use SAREhub\Commons\Secret\SecretValueProvider;


class DockerSecretValueProvider implements SecretValueProvider
{
    /**
     * @var SecretHelper
     */
    private $secretHelper;

    public function __construct(SecretHelper $secretHelper)
    {
        $this->secretHelper = $secretHelper;
    }

    public function get(string $secretName): string
    {
        try {
            return $this->secretHelper->getValue($secretName);
        } catch (SecretNotFoundException $e) {
            throw new SecretValueNotFoundException("value of secret '$secretName' not found");
        }
    }
}