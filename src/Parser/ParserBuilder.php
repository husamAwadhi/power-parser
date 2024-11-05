<?php

namespace HusamAwadhi\PowerParser\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Exception\InvalidPLuginException;
use HusamAwadhi\PowerParser\Exception\MissingElementException;
use HusamAwadhi\PowerParser\Parser\Extension\ParserPluginInterface;

class ParserBuilder
{
    /** @var ParserPluginInterface[] list of accepted extensions */
    protected array $extensions = [];

    protected Blueprint $blueprint;

    protected string $path;

    public function __construct(
        public readonly int $maxFileLength = 15000
    ) {
    }

    public function registerExtension(ParserPluginInterface $ext): self
    {
        if (isset($this->extensions[$ext::class])) {
            throw new InvalidPLuginException('Extension already registered');
        }
        $this->extensions[$ext::class] = $ext;

        return $this;
    }

    public function addBlueprint(Blueprint $blueprint): self
    {
        $this->blueprint = $blueprint;

        return $this;
    }

    public function addFile(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    protected function validateArguments(): void
    {
        if (!isset($this->blueprint)) {
            throw new MissingElementException('blueprint is not added.');
        }

        if (!isset($this->path)) {
            throw new MissingElementException('file path is not provided.');
        }

        if (!file_exists($this->path) || !is_readable($this->path)) {
            throw new MissingElementException("file: {$this->path} does not exist or unreadable.");
        }
    }

    public function build(): Parser
    {
        $this->validateArguments();

        $parser = new Parser(
            $this->blueprint,
            $this->path,
            $this->extensions,
        );

        return $parser;
    }
}
