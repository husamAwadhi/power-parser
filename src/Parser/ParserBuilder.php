<?php

namespace HusamAwadhi\PowerParser\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Exception\InvalidPLuginException;
use HusamAwadhi\PowerParser\Exception\MissingElementException;
use HusamAwadhi\PowerParser\Parser\Extension\ParserPluginInterface;

class ParserBuilder
{
    use IOCapable;

    /** @var ParserPluginInterface[] list of accepted extensions */
    protected array $extensions = [];

    protected Blueprint $blueprint;

    protected string $fileContent;

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
        $this->fileContent = $this->load($path, length: $this->maxFileLength);

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
