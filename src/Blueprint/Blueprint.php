<?php

namespace HusamAwadhi\PowerParser\Blueprint;

use Exception;
use HusamAwadhi\PowerParser\Blueprint\Components\Components;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidBlueprint;
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

    public static function createBlueprint(string $stream, $isPath = false) : self
    {
        try {
            $parsedFile = self::parseYaml($stream, $isPath);

            self::isValid($parsedFile);

            return new self(
                $stream,
                $parsedFile['meta']['file']['name'],
                $parsedFile['version'],
                $parsedFile['meta']['file']['extension'],
                new Components($parsedFile['blueprint'])
            );
        } catch (InvalidBlueprint $e) {
            var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * Validate yaml array
     *
     * @param array $yaml
     *
     * @throws InvalidBlueprint
     */
    public static function isValid($yaml): void
    {
        if ($yaml === false) {
            throw new InvalidBlueprint('Failed to parse yaml file');
        }

        if (!$yaml['meta'] ?? false) {
            throw new InvalidBlueprint(sprintf(self::MISSING_SECTION, ['mata']));
        }

        if (!$yaml['blueprint'] ?? false) {
            throw new InvalidBlueprint(sprintf(self::MISSING_SECTION, ['blueprint']));
        }

        foreach ($yaml['blueprint'] as $component) {
            if (!$component['type'] ?? false) {
                throw new InvalidBlueprint(sprintf(self::MISSING_ELEMENT, ['blueprint', 'type']));
            }

            if (!Type::tryFrom($component['type'])) {
                throw new InvalidBlueprint(
                    sprintf(
                        self::INVALID_VALUE,
                        ['type', $component['type'], implode(',', Type::cases())]
                    )
                );
            }

            if (!$component['fields'] ?? false) {
                throw new InvalidBlueprint(sprintf(self::MISSING_ELEMENT, ['blueprint', 'fields']));
            }
        }
    }

    private static function parseYaml($input, $isPath)
    {
        return match ($isPath) {
            true => \yaml_parse_file($input),
            false => \yaml_parse($input),
        };
    }
}
