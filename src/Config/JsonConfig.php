<?php

namespace PatrickRose\Invoices\Config;

use PatrickRose\Invoices\Exceptions\LockException;
use PatrickRose\Invoices\Exceptions\UnknownConfigKeyException;
use PatrickRose\Invoices\JsonTrait;

class JsonConfig implements ConfigInterface
{

    use JsonTrait;

    private $config;

    public function __construct($filename)
    {
        $this->config = $this->readAndLockFile($filename);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }

    public function get(string $key)
    {
        if (!array_key_exists($key, $this->config)) {
            throw new UnknownConfigKeyException("Unknown key $key");
        }

        return $this->config[$key];
    }

    public function set(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    public function getDefault(string $key, $default)
    {
        return $this->has($key) ? $this->get($key) : $default;
    }

    public function __destruct()
    {
        $this->writeAndUnlockFile($this->config);
    }

}
