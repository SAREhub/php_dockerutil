<?php

namespace SAREhub\DockerUtil\Secret;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class SecretHelperTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $secretsPath;

    /**
     * @var SecretHelper
     */
    private $helper;

    protected function setUp()
    {
        $this->secretsPath = vfsStream::setup();
        $this->helper = new SecretHelper($this->secretsPath->url());
    }

    public function testGetValueWhenExists()
    {
        $this->createSecretFile("my_secret", "secret_value\n");
        $this->assertEquals("secret_value", $this->helper->getValue("my_secret"));
    }

    public function testGetValueWhenNotexists()
    {
        $this->expectException(SecretNotFoundException::class);
        $this->expectExceptionMessage("Secret 'my_secret' not found");
        $this->helper->getValue("my_secret");
    }

    public function testGetFilePath()
    {
        $this->assertEquals($this->secretsPath->url() . '/my_secret', $this->helper->getFilePath("my_secret"));
    }

    public function testGetFilePathWhenNameHasRelativePathSymbols()
    {
        $this->assertEquals($this->secretsPath->url() . '/my_secret', $this->helper->getFilePath("../../my_secret"));
    }

    public function testExistsWhenExists()
    {
        $this->createSecretFile("my_secret", "secret_value\n");
        $this->assertTrue($this->helper->exists("my_secret"));
    }

    public function testExistsWhenNotExists()
    {
        $this->assertFalse($this->helper->exists("my_secret"));
    }

    public function testGetList()
    {
        $this->createSecretFile("my_secret1", "secret_value1\n");
        $this->createSecretFile("my_secret2", "secret_value2\n");

        $this->assertEquals(['my_secret1', 'my_secret2'], $this->helper->getList());
    }

    private function createSecretFile(string $secretName, string $secretValue)
    {
        $this->secretsPath->addChild(vfsStream::newFile($secretName)->setContent($secretValue));
    }
}
