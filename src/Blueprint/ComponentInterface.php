<?php

namespace HusamAwadhi\PowerParser\Blueprint;

interface ComponentInterface
{
    /**
     * get Mandatory Elements as array function.
     */
    public static function getMandatoryElements(): array;

    /**
     * get Optional Elements as array function.
     */
    public static function getOptionalElements(): array;

    /**
     * Entrypoint function.
     */
    public static function from(array $elements, BlueprintHelper $helper): self;

    /**
     * Validate passed parameters before instantiating self.
     */
    public static function validation(array &$elements): void;
}
