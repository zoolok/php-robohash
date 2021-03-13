<?php
require "vendor/autoload.php";

$robo = \Avram\Robohash\Robohash::make('Avram', 140, 'set5');
$robo->save('robohash.png');
