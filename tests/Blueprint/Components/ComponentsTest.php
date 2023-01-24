<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Blueprint\Components;

use PHPUnit\Framework\TestCase;
use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Exception\InvalidComponentException;

class ComponentsTest extends TestCase
{
    protected string $blueprintsDirectory = STORAGE_DIRECTORY . '/blueprints/';

    /**
     * @dataProvider invalidFilesProvider
     */
    public function testThrowingExceptionOnInvalidBlueprint(string $fileName, string $exception)
    {
        $this->expectException($exception);
        $path = $this->blueprintsDirectory . $fileName . '.yaml';
        Blueprint::createBlueprint($path, true);
    }
    public function invalidFilesProvider()
    {
        return [
            ['invalid_component_1', InvalidComponentException::class],
        ];
    }
}
