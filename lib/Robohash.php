<?php

namespace Zoolok\Robohash;

use Intervention\Image\ImageManager as Image;

class Robohash
{
    private const    IMAGE_WIDTH  = 300;
    private const    IMAGE_HEIGHT = 300;
    private string $imageDir = '../vendor/zoolok/robohash/images/';
    private static array $colors = [
        'blue',
        'brown',
        'green',
        'grey',
        'orange',
        'pink',
        'purple',
        'red',
        'white',
        'yellow',
    ];
    private static array $sets = [
        'set1',
        'set2',
        'set3',
        'set4',
        'set5'
    ];
    private static array $bgSets = [
        'bg1',
        'bg2'
    ];
    private string $set       = '';
    private string $bgSet     = '';
    private int    $hashIndex = 4;
    private array  $hashList  = [];
    private int    $size      = 300;


    public function __construct(array $options)
    {
        if (isset($options['text'])) {
            $this->createHashes($options['text']);
        }

        if (! empty($options['color'])) {
            $this->setColor($options['color']);
        }

        if (isset($options['set'])) {
            $this->setImageSet($options['set']);
        }

        if (isset($options['bgset'])) {
            $this->setBackgroundSet($options['bgset']);
        }

        if (isset($options['size'])) {
            $this->setImageSize($options['size']);
        }

        if (!is_dir($this->imageDir)) {
            $this->setImagesPath('images/');
        }
    }

    /**
     * @param string $text
     *
     * @return void
     */
    private function createHashes(string $text): void
    {
        $hashes = str_split(hash('sha512', $text), 11);

        foreach ($hashes as $hash) {
            $this->hashList[] = base_convert($hash, 16, 10);
        }
      //  dd($this->hashList);
    }

    /**
     * Set avatar color
     *
     * @param string|null $color
     *
     * @return void
     */
    public function setColor(?string $color): void
    {
        $this->set = 'set1/';

        $this->set .= $color && in_array($color, static::$colors)
                ? $color
                : self::$colors[bcmod($this->hashList[0], count(static::$colors))];
    }

    /**
     * Set avatar set
     *
     * @param string $set
     *
     * @return void
     */
    public function setImageSet(string $set): void
    {
        if ($set == 'set1' || !in_array($set, self::$sets)) {
            return;
        }

        if ($set == 'any') {
            $set = self::$sets[bcmod($this->hashList[1], count(self::$sets))];
        }

        $this->set = $set;
    }

    public function setBackgroundSet($bgset)
    {
        if (!in_array($bgset, self::$bgSets)) {
            $bgset = self::$bgSets[bcmod($this->hashList[2], count(self::$bgSets))];
        }
        $bgfiles     = glob($this->imageDir."backgrounds/$bgset/*");
        $this->bgSet = $bgfiles[bcmod($this->hashList[3], count($bgfiles))];

        return $this;
    }

    protected function getImageList()
    {
        $image_list = array();
        $dirs       = glob($this->imageDir."sets/{$this->set}/*");

        foreach ($dirs as $dir) {
            $files     = glob("$dir/*");
            $img_index = bcmod($this->hashList[$this->hashIndex], count($files));
            $this->hashIndex++;
            $s = explode('#', $files[$img_index], 2);
            krsort($s);
            $temp[] = implode("|", $s);
        }
        sort($temp);

        foreach ($temp as $file) {
            $s = explode('|', $file, 2);
            krsort($s);
            $image_list[] = implode("#", $s);
        }
        if ($this->bgSet) {
            array_unshift($image_list, $this->bgSet);
        }
        return $image_list;
    }

    public function setImagesPath($path)
    {
        $this->imageDir = $path;
        return $this;
    }

    public function setImageSize($size)
    {
        $this->size = $size;
        return $this;
    }

    protected function getImageSize()
    {
        $width  = self::IMAGE_WIDTH;
        $height = self::IMAGE_WIDTH;

        if ($this->size) {
            $width_height = explode('x', $this->size);
            if (count($width_height) == 1) {
                $width_height = [$this->size, $this->size];
            }

            $width  = isset($width_height[0]) ? (int)$width_height[0] : self::IMAGE_WIDTH;
            $height = isset($width_height[1]) ? (int)$width_height[1] : self::IMAGE_HEIGHT;

            if ($width > 1024 || $width < 10) {
                $width = self::IMAGE_WIDTH;
            }
            if ($height > 1024 || $height < 10) {
                $height = self::IMAGE_HEIGHT;
            }
        }
        return array($width, $height);
    }

    protected function generateWithIntervention($imageList)
    {
        $first = array_shift($imageList);
        $body  = (new Image)->make($first);

        [$width, $height] = $this->getImageSize();

        $body->resize($width, $height);

        foreach ($imageList as $image_file) {
            $image = (new Image)->make($image_file);
            $image->resize($width, $height);
            $body->insert($image, 'center');
            $image->destroy();
        }

        return $body;
    }

    public function generateImage()
    {
        return $this->generateWithIntervention($this->getImageList());
    }

    public static function randomText($length = 8)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($chars), 0, $length);
    }

    public static function randomSet()
    {
        return self::$sets[array_rand(self::$sets)];
    }

    public static function randomColor()
    {
        return self::$colors[array_rand(self::$colors)];
    }

    public static function randomBgSet()
    {
        return self::$bgSets[array_rand(self::$bgSets)];
    }

    public static function make($text, $size = 300, $set = 'set1', $color = 'blue', $bgset = null)
    {
        return (new static([
            'text'  => $text,
            'size'  => $size,
            'set'   => $set,
            'color' => $color,
            'bgset' => $bgset,
        ]))->generateImage();
    }

}
