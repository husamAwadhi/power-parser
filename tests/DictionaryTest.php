<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests;

use HusamAwadhi\PowerParser\Dictionary;
use PHPUnit\Framework\TestCase;

class DictionaryTest extends TestCase
{
    private string $storageTestDirectory = STORAGE_DIRECTORY . '/dictionaries/';

    public function testCreateObject(): void
    {
        $this->assertInstanceOf(
            Dictionary::class,
            new Dictionary($this->storageTestDirectory . 'valid.php')
        );
    }

    /**
     * @dataProvider getDataProvider
     */
    public function testCanGetValue(string $key, ?string $default, string $file, string $actual): void
    {
        $dictionary = new Dictionary($this->storageTestDirectory . $file);

        $this->assertEquals(
            $dictionary->get($key, $default),
            $actual,
        );
    }
    public function getDataProvider(): array
    {
        return [
            ['test.my.limits', null, 'valid.php', 'you %s'],
            ['test.limits', 'fallback', 'valid.php', 'fallback'],
        ];
    }

    /**
     * @dataProvider formattedValuesDataProvider
     */
    public function testCanFormattedGetValue(string $key, ?string $default, string $file, string $actual, array $values): void
    {
        $dictionary = new Dictionary($this->storageTestDirectory . $file);

        $this->assertEquals(
            $dictionary->getFormatted($key, $default, $values),
            $actual,
        );
    }
    public function formattedValuesDataProvider(): array
    {
        return [
            ['test.my.limits', null, 'valid.php', 'you fool', ['fool']],
            ['test.limits', 'yo %s', 'valid.php', 'yo dog', ['dog']],
            ['test.limits', 'hello world', 'valid.php', 'hello world', []],
        ];
    }
}
