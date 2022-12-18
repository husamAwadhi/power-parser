<?php

namespace HusamAwadhi\PowerParser\Blueprint;

interface ComponentInterface
{
    /**
     * get Mandatory Elements as array function
     * @return array
     */
    public static function getMandatoryElements() : array;

    /**
     * get Optional Elements as array function
     * @return array
     */
    public static function getOptionalElements() : array;

    /**
     * Entrypoint function
     * @param array $elements
     * @return self
     */
    public static function createFromParameters(array $elements): self;

    /**
     * Validate passed parameters before instantiating self
     * @param array $elements
     */
    public static function validation(array $elements): void;
}
