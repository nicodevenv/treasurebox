<?php

namespace App\Entities;

use App\Service\CheckerService;

class Map {
    const REFERENCE = 'C';

    private $mapWidth;

    private $mapHeight;

    private $mapFrames = [];

    private $outputFile = null;

    /**
     * Map constructor.
     * @param $width
     * @param $height
     * @param $outputFile
     * @throws \Exception
     */
    public function __construct($width, $height, $outputFile)
    {
        $this->outputFile = $outputFile;

        if (!CheckerService::isIntegerAndMoreThanZero(false, $width)) {
            throw new \Exception('Map width invalid ! Attempted for integer > 0');
        }

        if (!CheckerService::isIntegerAndMoreThanZero(false, $height)) {
            throw new \Exception('Map height invalid ! Attempted for integer > 0');
        }

        $this->mapWidth = $width;
        $this->mapHeight = $height;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $this->mapFrames[$y][$x] = [];
            }
        }
    }

    public function getWidth()
    {
        return $this->mapWidth;
    }

    public function getMaxX()
    {
        return $this->mapWidth-1;
    }

    public function getHeight()
    {
        return $this->mapHeight;
    }

    public function getMaxY()
    {
        return $this->mapHeight-1;
    }

    public function getMapFrames()
    {
        return $this->mapFrames;
    }

    public function addOption($x, $y, AbstractOption $option)
    {
        $this->mapFrames[$y][$x][] = $option;
    }

    public function removeOption($x, $y, $index)
    {
        unset($this->mapFrames[$y][$x][$index]);
    }
}