#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Pdpaola\CoffeeMachine\Application\Command\MakeDrinkCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new MakeDrinkCommand());

$application->run();
