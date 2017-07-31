<?php

namespace PatrickRose\Invoices;

use PatrickRose\Invoices\Exceptions\LockException;

trait JsonTrait
{

    /**
     * @var resource The current locked stream
     */
    private $stream;

    /**
     * Lock the given file, and read it
     *
     * @param string $filename The file to read
     * @return array
     */
    public function readAndLockFile(string $filename): array
    {
        if (!file_exists($filename)) {
            touch($filename);
        }

        $this->stream = fopen($filename, 'r+');

        if (!flock($this->stream, LOCK_EX | LOCK_NB)) {
            throw new LockException("Unable to lock $filename");
        }

        $toReturn = json_decode(stream_get_contents($this->stream), true);

        if ($toReturn === null)
        {
            $toReturn = [];
        }

        return $toReturn;
    }

    public function writeAndUnlockFile(array $jsonToWrite)
    {
        // Truncate the stream since it might have had prettified json
        ftruncate($this->stream, 0);
        fseek($this->stream, 0);
        fwrite($this->stream, json_encode($jsonToWrite));

        flock($this->stream, LOCK_UN);
        fclose($this->stream);
    }

}
