<?php

namespace HusamAwadhi\PowerParser\Blueprint;

use HusamAwadhi\PowerParser\Exception\InvalidBlueprintException;
use Symfony\Component\Yaml\Yaml;

class BlueprintBuilder
{
    public const EMPTY_STREAM = 'Input stream cannot be empty or file not found';

    protected bool $isValid;

    protected array $content;

    protected string $stream;

    public function __construct(
        protected BlueprintHelper $helper,
    ) {
    }

    public function load(string $path): self
    {
        $this->parseYaml($path, true);
        $this->stream = $path;

        return $this;
    }

    public function parse(string $yaml): self
    {
        $this->parseYaml($yaml, false);
        $this->stream = 'loaded-file';

        return $this;
    }

    public function build(): Blueprint
    {
        if (!isset($this->content)) {
            throw new InvalidBlueprintException('No Blueprint is loaded.');
        }

        return Blueprint::from(
            content: $this->content,
            stream: $this->stream,
            helper: $this->helper
        );
    }

    /**
     * @throws InvalidBlueprintException
     */
    private function parseYaml(string $input, bool $isPath): void
    {
        if (!$input || ($isPath && !is_file($input))) {
            throw new InvalidBlueprintException(self::EMPTY_STREAM);
        }

        $content = match ($isPath) {
            true => Yaml::parseFile($input),
            false => Yaml::parse($input),
        };

        if (!$content) {
            throw new InvalidBlueprintException('Failed to parse Yaml file.');
        }

        $this->content = $content;
    }
}
