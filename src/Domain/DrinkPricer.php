<?php

namespace Pdpaola\CoffeeMachine\Domain;

class DrinkPricer
{
    public static function getPrice($type)
    {
        switch ($type) {
            case 'tea': return 0.4;
            case 'coffee': return 0.5;
            case 'chocolate': return 0.6;
        }
        return 0; // Default price
    }
}
