<?php

namespace Pdpaola\CoffeeMachine\Domain;

class DrinkValidator
{
    public static function validateDrinkType($type)
    {
        return in_array($type, ['tea', 'coffee', 'chocolate']);
    }

    public static function validateSugars($sugars)
    {
        return $sugars >= 0 && $sugars <= 2;
    }
}
