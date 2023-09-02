<?php

namespace HusamAwadhi\PowerParser\Parser\Utils;

use HusamAwadhi\PowerParser\Exception\InvalidArgumentException;
use HusamAwadhi\PowerParser\Exception\UnableToCreateFileException;

trait IOCapable
{
    public readonly int $maxFileLength;

    private string $path = '';

    /** @var resource */
    private $file;

    /**
     * @throws UnableToCreateFileException
     *
     * @return string a valid path to file
     */
    protected function writeTemporaryFile(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), __FUNCTION__);

        if (!$path) {
            throw new UnableToCreateFileException('Unable to get a temporary file path');
        }

        $this->path = $path;

        $file = fopen($this->path, 'r+b');

        if (!is_resource($file)) {
            throw new UnableToCreateFileException("Unable to create temporary file in {$this->path}");
        }

        $this->file = $file;

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

    private function loadFromLink(string $url): string
    {
        // TODO: use curl ... maybe
        $content = \file_get_contents($url, length: $this->maxFileLength ?? null);

        if ($content === false) {
            throw new InvalidArgumentException("unable to load file from link: {$url}");
        }

        return $content;
    }

    private function loadFromPath(string $path): string
    {
        if (!is_readable($path)) {
            throw new InvalidArgumentException("unable to read file from path: {$path}");
        }

        $content = \file_get_contents($path, length: $this->maxFileLength ?? null);

        if ($content === false) {
            throw new InvalidArgumentException("unable to load file from path: {$path}");
        }

        return $content;
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return string the read data or false on failure
     */
    protected function load(string $parameter): string
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
