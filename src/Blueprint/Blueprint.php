<?php

namespace HusamAwadhi\PowerParser\Blueprint;

use HusamAwadhi\PowerParser\Blueprint\Components\Components;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidBlueprintException;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidComponentException;

class Blueprint implements BlueprintInterface
{
    private function __construct(
        public readonly string $rawFile,
        public readonly string $name,
        public readonly string $version,
        public readonly string $extension,
        public readonly Components $components
    ) {
    }

    /**
     * Blueprint entrypoint.
     *
     * @param string $stream File path or content
     * @param bool $isPath true if passed stream is a path to file
     *
     * @throws InvalidBlueprintException
     * @throws InvalidComponentException
     */
    public static function createBlueprint(string $stream, bool $isPath = false): self | null
    {
        $parsedFile = self::parseYaml($stream, $isPath);

        self::validate($parsedFile);

        return new self(
            $stream,
            $parsedFile['meta']['file']['name'],
            $parsedFile['version'],
            $parsedFile['meta']['file']['extension'],
            Components::createFromParameters($parsedFile['blueprint']),
        );
    }

    /**
     * Validate yaml array.
     *
     * @param array $yaml
     *
     * @throws InvalidBlueprintException
     */
    public static function validate(mixed $yaml): void
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

    /**
     * @throws InvalidBlueprintException
     */
    private static function parseYaml(string $input, bool $isPath): mixed
    {
        if (!$input || ($isPath && !is_file($input))) {
            throw new InvalidBlueprintException(self::EMPTY_STREAM);
        }

        return match ($isPath) {
            true => \yaml_parse_file($input),
            false => \yaml_parse($input),
        };
    }
}
