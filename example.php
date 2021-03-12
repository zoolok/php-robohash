<?php
require "vendor/autoload.php";

$robo = \Avram\Robohash\Robohash::make(md5('Sgt_Baker'), 140, 'set5');
$robo->save('baker.png');