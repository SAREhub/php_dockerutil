<?php

namespace SAREhub\DockerUtil\Secret;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SAREhub\Commons\Secret\SecretValueNotFoundException;

class DockerSecretValueProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var SecretHelper | MockInterface
     */
    private $secretHelper;

    /**
     * @var DockerSecretValueProvider
     */
    private $valueProvider;

    protected function setUp()
    {
        $this->secretHelper = \Mockery::mock(SecretHelper::class);
        $this->valueProvider = new DockerSecretValueProvider($this->secretHelper);
    }

    public function testGetWhenExistsThenReturnValue()
    {
        $secretName = "test_secret";
        $expectedValue = "test_value";
        $this->secretHelper->expects("getValue")->withArgs([$secretName])->andReturn($expectedValue);
        $this->assertEquals($expectedValue, $this->valueProvider->get($secretName));
    }

    public function testGetWhenNotExistsThenThrowException()
    {
        $secretName = "test_secret";
        $this->secretHelper->expects("getValue")->withArgs([$secretName])
            ->andThrow(SecretNotFoundException::class);

        $this->expectException(SecretValueNotFoundException::class);
        $this->valueProvider->get($secretName);
    }
}
