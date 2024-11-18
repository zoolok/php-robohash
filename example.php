<?php

use Zoolok\Robohash\Robohash;

require "vendor/autoload.php";

$robo = Robohash::make('Zoolok', 140, 'set5');
$robo->save('robohash.png');
