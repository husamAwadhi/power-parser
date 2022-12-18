<?php

namespace HusamAwadhi\PowerParser;

/**
 * helpers for application
 *
 * @author Husam A <husam.awadhi@gmail.com>
 */
class Helper
{

    /**
     * convert multi dimensions array to one dimension
     * example:
     * ? before
     * [ 'key1' => [ 'key2' => 'value' ] ]
     * ? after
     * [ 'key1.key2' => 'value' ]
     *
     * @param array $array
     * @param boolean $nestedKey
     * @param array $final
     * @return array
     * @throws \Exception on duplicate keys
     */
    public static function toOneDimensionArray(
        array $array,
        string $separator = '.',
        string $nestedKey = '',
        array $final = [],
    ): array {
        foreach ($array as $key => $element) {
            $fullKey = (\strlen($nestedKey) > 0 ? $nestedKey . $separator : '')  . $key;

            if (is_array($array[$key])) {
                $final  = array_merge(
                    $final,
                    self::toOneDimensionArray($array[$key], $separator, $fullKey, $final)
                );
            } else {
                if (array_key_exists($fullKey, $final)) {
                    throw new \Exception("key: ({$fullKey}) already exists. hint: Avoid using keys with the same separator");
                }
                $final[$fullKey] = $element;
            }
        }

        return $final;
    }
}
