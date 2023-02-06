<?php

namespace HusamAwadhi\PowerParser\Blueprint;

use HusamAwadhi\PowerParser\Blueprint\Components\Components;
use HusamAwadhi\PowerParser\Exception\InvalidBlueprintException;
use HusamAwadhi\PowerParser\Exception\InvalidComponentException;

class Blueprint implements BlueprintInterface
{
    public function __construct(
        public readonly string $path,
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
     *
     * @throws InvalidBlueprintException
     * @throws InvalidComponentException
     */
    public static function from(array $content, string $stream, BlueprintHelper $helper): self
    {
        self::validate($content);

        return new self(
            $stream,
            $content['meta']['file']['name'],
            $content['version'],
            $content['meta']['file']['extension'],
            $helper->createComponents($content['blueprint']),
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
}
