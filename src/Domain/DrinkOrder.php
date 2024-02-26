<?php

namespace Pdpaola\CoffeeMachine\Domain;

class DrinkOrder
{
    public $type;
    public $sugars;
    public $stick;
    public $extraHot;

    public function __construct($type, $sugars, $extraHot)
    {
        $this->type = $type;
        $this->sugars = $sugars;
        if ($sugars) {
            $this->stick = true;
        } else {
            $this->stick = false;
        }
        $this->extraHot = $extraHot;
    }
}
