<?php

namespace HusamAwadhi\PowerParser\Blueprint;

use Exception;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidBlueprint;
use Iterator;

class Blueprint implements BlueprintInterface
{
    private string $rawFile;
    protected string $name;
    protected string $version;
    protected string $extension;
    protected Iterator $components;

    private function __construct($rawFile, $name, $version, $extension, $components)
    {
        $this->rawFile = $rawFile;
    }

    public static function createBlueprint(string $stream, $isPath = false)
    {
        try {
            $parsedFile = self::parseYaml($stream, $isPath);

            if ($parsedFile === false) {
                throw new InvalidBlueprint('Failed to parse yaml file');
            }
            $components = null;
            return new self(
                $stream,
                $parsedFile['meta']['file']['name'],
                $parsedFile['version'],
                $parsedFile['meta']['file']['extension'],
                $components
            );
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
    }

    private function parseYaml($input, $isPath)
    {
        return match ($isPath) {
            true => \yaml_parse_file($input),
            false => \yaml_parse($input),
        };
    }
}
