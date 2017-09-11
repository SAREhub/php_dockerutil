<?php

namespace SAREhub\DockerUtil\Secret;

class SecretHelper
{
    const DEFAULT_SECRETS_PATH = '/run/secrets';

    private $secretsPath;

    /**
     * @param $secretsPath string Path where all secrets stored inside container.
     */
    public function __construct(string $secretsPath = self::DEFAULT_SECRETS_PATH)
    {
        $this->secretsPath = $secretsPath;
    }

    /**
     * Returns value of given secret
     * @param string $secretName
     * @return string
     */
    public function getValue(string $secretName): string
    {
        if ($this->exists($secretName)) {
            return trim(file_get_contents($this->getFilePath($secretName)));
        }

        throw new SecretNotFoundException($secretName);
    }

    public function getFilePath(string $secretName): string
    {
        return $this->getSecretsPath() . '/' . basename($secretName);
    }

    /**
     * Checks if secret exists
     * @param string $secretName
     * @return bool
     */
    public function exists(string $secretName): bool
    {
        return file_exists($this->getFilePath($secretName));
    }

    /**
     * Returns all secret names
     * @return array
     */
    public function getList(): array
    {
        return array_values(array_filter(scandir($this->getSecretsPath()), function ($file) {
            return $file !== "." && $file !== "..";
        }));

    }

    public function getSecretsPath(): string
    {
        return $this->secretsPath;
    }
}