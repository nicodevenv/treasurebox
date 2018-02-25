<?php

namespace App\Entities;

use App\Service\CheckerService;

class Map
{
    const REFERENCE = 'C';

    private $mapWidth;

    private $mapHeight;

    private $mapFrames = [];

    /**
     * Map constructor.
     *
     * @param $width
     * @param $height
     *
     * @throws \Exception
     */
    public function __construct($width, $height)
    {
        if (!CheckerService::isIntegerAndMoreThanZero(false, $width)) {
            throw new \Exception('Map width invalid ! Attempted for integer > 0');
        }

        if (!CheckerService::isIntegerAndMoreThanZero(false, $height)) {
            throw new \Exception('Map height invalid ! Attempted for integer > 0');
        }

        $this->mapWidth  = intval($width);
        $this->mapHeight = intval($height);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $this->mapFrames[$y][$x] = [];
            }
        }
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->mapWidth;
    }

    /**
     * @return int
     */
    public function getMaxX(): int
    {
        return $this->mapWidth - 1;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->mapHeight;
    }

    /**
     * @return int
     */
    public function getMaxY(): int
    {
        return $this->mapHeight - 1;
    }

    /**
     * @return array
     */
    public function getMapFrames(): array
    {
        return $this->mapFrames;
    }

    /**
     * @param int            $x
     * @param int            $y
     * @param AbstractOption $option
     *
     * @return $this
     */
    public function addOption(int $x, int $y, AbstractOption $option)
    {
        $this->mapFrames[$y][$x][] = $option;

        return $this;
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $index
     *
     * @return $this
     */
    public function removeOption(int $x, int $y, int $index)
    {
        unset($this->mapFrames[$y][$x][$index]);

        return $this;
    }
}