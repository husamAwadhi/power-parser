<?php

namespace HusamAwadhi\PowerParser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;
use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Parser\Extension\Spreadsheet\Spreadsheet;
use HusamAwadhi\PowerParser\Parser\ParserBuilder;

class PowerParser implements PowerParserInterface
{
    /**
     * @inheritDoc
     */
    public static function getParserBuilder(
        string $stream,
        string $file,
        int $maxFileLength = 15_000,
        ?BlueprintBuilder $blueprintBuilder = null,
        ?BlueprintHelper $blueprintHelper = null,
    ): ParserBuilder {
        return (new ParserBuilder(maxFileLength: $maxFileLength))
            ->registerExtension(ext: new Spreadsheet())
            ->addBlueprint(
                blueprint: self::createBlueprint(
                    stream: $stream,
                    builder: ($blueprintBuilder ?? new BlueprintBuilder(
                        $blueprintHelper ?? new BlueprintHelper()
                    ))
                )
            )
            ->addFile(path: $file);
    }

    /**
     * @inheritDoc
     */
    public static function createBlueprint(string $stream, BlueprintBuilder $builder): Blueprint
    {
        if (is_file($stream) && is_readable($stream)) {
            $builder->load($stream);
        } else {
            $builder->parse($stream);
        }

        return $builder->build();
    }
}
