<?php

namespace HusamAwadhi\PowerParser\Parser\Utils;

use HusamAwadhi\PowerParser\Exception\InvalidArgumentException;
use HusamAwadhi\PowerParser\Exception\UnableToCreateFileException;

trait IOCapable
{
    public readonly int $maxFileLength;

    private string $path = '';

    private $file;

    /**
     * @throws UnableToCreateFileException
     *
     * @return string a valid path to file
     */
    protected function writeTemporaryFile(string $content): string
    {
        $this->path = tempnam(sys_get_temp_dir(), __CLASS__);
        $this->file = fopen($this->path, 'r+b');

        if (!is_resource($this->file)) {
            throw new UnableToCreateFileException("Unable to create temporary file in {$this->path}");
        }

        if (!is_writable($this->path)) {
            throw new UnableToCreateFileException("Unable to modify temporary file in {$this->path}");
        }

        fwrite($this->file, $content);

        return $this->path;
    }

    protected function deleteTemporaryFile(): bool
    {
        return $this->path ? unlink($this->path) : true;
    }

    private function loadFromLink($url): string
    {
        // TODO: use curl ... maybe
        $content = \file_get_contents($url, length: $this->maxFileLength);

        if ($content === false) {
            throw new InvalidArgumentException("unable to load file from link: {$url}");
        }

        return $content;
    }

    private function loadFromPath($path): string
    {
        if (!is_readable($path)) {
            throw new InvalidArgumentException("unable to read file from link: {$path}");
        }

        return \file_get_contents($path, length: $this->maxFileLength);
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return string the read data or false on failure
     */
    protected function load(string $parameter, int $length): string
    {
        if (filter_var($parameter, \FILTER_VALIDATE_URL)) {
            return $this->loadFromLink($parameter);
        }

        if (is_file($parameter)) {
            return $this->loadFromPath($parameter);
        }

        throw new InvalidArgumentException('Parameter is not a valid file or link.');
    }
}
