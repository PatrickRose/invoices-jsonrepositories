<?php

namespace PatrickRose\Invoices\Config;

use PatrickRose\Invoices\Exceptions\LockException;
use PatrickRose\Invoices\Tests\Config\ConfigInterfaceTestCase;

class JsonConfigTest extends ConfigInterfaceTestCase
{

    private $testFiles = [];

    protected function tearDown()
    {
        parent::tearDown();

        foreach ($this->testFiles as $file)
        {
            unlink($file);
        }

        $this->testFiles = [];
    }

    /**
     * Get the config implementation under test
     *
     * @param array $existingConfig The existing configuration
     * @return ConfigInterface
     */
    protected function getConfigUnderTest(array $existingConfig = []): ConfigInterface
    {
        $testFile = tempnam(sys_get_temp_dir(), $this->getName());
        $this->testFiles[] = $testFile;
        file_put_contents($testFile, json_encode($existingConfig));

        return new JsonConfig($testFile);
    }

    public function testTheFileIsLocked()
    {
        $testFile = tempnam(sys_get_temp_dir(), $this->getName());
        $this->testFiles[] = $testFile;
        file_put_contents($testFile, json_encode(['test' => 12345]));

        $config = new JsonConfig($testFile);
        $thrown = false;

        try
        {
            new JsonConfig($testFile);
        }
        catch (LockException $ex)
        {
            $thrown = true;
        }

        $this->assertTrue($thrown, 'Did not throw a exception');

        unset($config);

        new JsonConfig($testFile);
    }



    public function test_ItHandlesTruncationOfJson()
    {
        $testFile = tempnam(sys_get_temp_dir(), $this->getName());
        $this->testFiles[] = $testFile;
        file_put_contents($testFile, "{\n\n\n\"test\":\"value\"\n\n\n\n\n}");

        $config = new JsonConfig($testFile);

        $this->assertTrue($config->has("test"));

        unset($config);

        $config = new JsonConfig($testFile);

        $this->assertTrue($config->has("test"));
    }
}
