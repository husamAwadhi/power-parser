<?php

namespace HusamAwadhi\PowerParser;

final class Dictionary
{
    private const FILE = 'en';
    private const DICTIONARY_DIRECTORY = '/storage/dictionary/';

    private readonly ?string $filePath;

    public function __construct(?string $filePath = null)
    {
        $this->filePath = $this->generateFilePath($filePath);
    }

    private function generateFilePath(?string $filePath = null): ?string
    {
        return is_file($filePath ?? '')
            ? $filePath
            : dirname(__DIR__) . self::DICTIONARY_DIRECTORY . self::FILE . '.php';
    }

    private function loadDictionary(): array
    {
        static $dictionary = [];

        return $dictionary[$this->filePath]
            ?? $dictionary[$this->filePath] = Helper::toOneDimensionArray(
                require $this->filePath
            );
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return isset($this->loadDictionary()[$key])
            ? $this->loadDictionary()[$key]
            : $default;
    }

    public function getFormatted(string $key, mixed $default = null, array $values): mixed
    {
        return \sprintf(
            $this->get($key, $default),
            ...$values
        );
    }
}
