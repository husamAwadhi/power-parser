<?php

namespace HusamAwadhi\PowerParser\Blueprint;

use HusamAwadhi\PowerParser\Blueprint\Components\Components;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidBlueprintException;
use Iterator;

class Blueprint implements BlueprintInterface
{
    public readonly string $rawFile;
    public readonly string $name;
    public readonly string $version;
    public readonly string $extension;
    public readonly Iterator $components;

    private function __construct($rawFile, $name, $version, $extension, $components)
    {
        $this->rawFile = $rawFile;
        $this->name = $name;
        $this->version = $version;
        $this->extension = $extension;
        $this->components = $components;
    }

    /**
     * Blueprint entrypoint
     *
     * @param string $stream File path or content
     * @param bool $isPath true if passed stream is a path to file
     *
     * @throws InvalidBlueprintException
     * @throws InvalidComponentException
     */
    public static function createBlueprint(string $stream, $isPath = false): self | null
    {
        $parsedFile = self::parseYaml($stream, $isPath);

        self::isValid($parsedFile);

        return new self(
            $stream,
            $parsedFile['meta']['file']['name'],
            $parsedFile['version'],
            $parsedFile['meta']['file']['extension'],
            Components::createFromArray($parsedFile['blueprint']),
        );
    }

    /**
     * Validate yaml array
     *
     * @param array $yaml
     *
     * @throws InvalidBlueprintException
     */
    public static function isValid($yaml): void
    {
        if (!$yaml) {
            throw new InvalidBlueprintException(self::CANNOT_PARSE);
        }

        if (!isset($yaml['version'])) {
            throw new InvalidBlueprintException(\sprintf(self::MISSING_ELEMENT, '__ROOT__', 'version'));
        }

        if (!isset($yaml['meta'])) {
            throw new InvalidBlueprintException(\sprintf(self::MISSING_SECTION, 'meta'));
        }

        if (!isset($yaml['meta']['file'])) {
            throw new InvalidBlueprintException(\sprintf(self::MISSING_SECTION, 'meta'));
        }

        if (!isset($yaml['meta']['file']['name'])) {
            throw new InvalidBlueprintException(\sprintf(self::MISSING_ELEMENT, 'meta -> file', 'name'));
        }

        if (!isset($yaml['meta']['file']['extension'])) {
            throw new InvalidBlueprintException(\sprintf(self::MISSING_ELEMENT, 'meta -> file', 'extension'));
        }

        if (!isset($yaml['blueprint'])) {
            throw new InvalidBlueprintException(\sprintf(self::MISSING_SECTION, 'blueprint'));
        }
    }

    private static function parseYaml($input, $isPath)
    {
        if (!$input) {
            throw new InvalidBlueprintException(self::EMPTY_STREAM);
        }

        return match ($isPath) {
            true => \yaml_parse_file($input),
            false => \yaml_parse($input),
        };
    }
}
