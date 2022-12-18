<?php

namespace HusamAwadhi\PowerParser;

use HusamAwadhi\PowerParser\Helper;

class Dictionary
{
    private const FILE = 'en';
    private const DICTIONARY_DIRECTORY = '/storage/dictionary/';

    private static function loadDictionary(): array
    {
        static $dictionary = false;
        dump($dictionary);
        return $dictionary
            ?? $dictionary = Helper::toOneDimensionArray(
                require(dirname(__DIR__) . self::DICTIONARY_DIRECTORY . self::FILE . '.php')
            );
    }

    public function getValue(string $key, mixed $default = null): mixed
    {
        return isset(self::loadDictionary()[$key])
            ? self::loadDictionary()[$key]
            : $default;
    }
}
