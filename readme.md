php-robohash 
============

PHP implementation of [Robohash](https://github.com/e1ven/Robohash). Uses ImageMagick or GD as fallback (via intervention/image) to process the images. Only basic functionality, image caching and request processing is up to you.

## Install ##

`composer require avram/robohash`

## Usage ##

Robohash::make() creates an instance of Robohash class and then calls generateImage() on it, which will then return an instance of [Intervention\Image](http://image.intervention.io/use/basics), which you can then use to further process the image or save()/encode() it.

    <?php
    require "vendor/autoload.php";
    
    $robo = \Avram\Robohash\Robohash::make('Avram', 140, 'set5');
    $robo->save('robohash.png');

## Credits to ##

- Colin Davis for original [Robohash](https://github.com/e1ven/Robohash)
- hush2 for [php-robohash](https://github.com/hush2/php-robohash).
