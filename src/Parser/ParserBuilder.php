<?php

namespace HusamAwadhi\PowerParser\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Exception\InvalidArgumentException;
use HusamAwadhi\PowerParser\Exception\InvalidExtensionException;
use HusamAwadhi\PowerParser\Exception\MissingElementException;
use HusamAwadhi\PowerParser\Parser\Extension\ParserExtensionInterface;

class ParserBuilder
{
    protected array $extensions = [];

    protected Blueprint $blueprint;

    protected string $fileContent;

    public function __construct(
        public readonly int $maxFileLength = 15000
    ) {
    }

    public function registerExtension(ParserExtensionInterface $ext): self
    {
        if (isset($this->extensions[$ext::class])) {
            throw new InvalidExtensionException('Extension already registered');
        }
        $this->extensions[$ext::class] = $ext;

        return $this;
    }

    public function addBlueprint(Blueprint $blueprint): self
    {
        $this->blueprint = $blueprint;

        return $this;
    }

    public function addFileFromPath(string $path): self
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException("file can not be found at: {$path}");
        }

        $this->fileContent = \file_get_contents($path, length: $this->maxFileLength);

        if ($this->fileContent === false) {
            throw new InvalidArgumentException("unable to load file: {$path}");
        }

        return $this;
    }

    public function addFileFromLink(string $url): self
    {
        if (!filter_var($url, \FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Link is not valid: {$url}");
        }

        // TODO: use curl ... maybe
        $this->fileContent = \file_get_contents($url, length: $this->maxFileLength);

        if ($this->fileContent === false) {
            throw new InvalidArgumentException("unable to load file from link: {$url}");
        }

        return $this;
    }

    protected function validateArguments(): void
    {
        if (!isset($this->blueprint)) {
            throw new MissingElementException('blueprint is not added.');
        }

        if (!isset($this->fileContent)) {
            throw new MissingElementException('file is not loaded.');
        }
    }

    public function build(): Parser
    {
        $this->validateArguments();

        $parser = new Parser(
            $this->blueprint,
            $this->fileContent,
            $this->extensions,
        );

        return $parser;
    }
}
