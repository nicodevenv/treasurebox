<?php

namespace App\Entities;

use App\Service\CheckerService;

class Map {
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

    public function displayMap()
    {
        $outputStr = '';
        foreach ($this->mapFrames as $y => $row) {
            foreach($row as $x => $data) {
                $outputStr .= '.';
            }
            $outputStr .= "\n";
        }
    }

    /**
     * @param AbstractOption $option
     * @param bool $isStackable
     *
     * @throws \Exception
     */
    public function addOption($option, $isStackable = false)
    {
        if (!$isStackable && count($this->mapFrames[$option->getY()][$option->getX()]) > 0) {
            throw new \Exception(
                sprintf(
                    'Unable to add %s.. another option is already using [%s,%s] position',
                    $option->getType(),
                    $option->getX(),
                    $option->getY()
                )
            );
        }

        $this->mapFrames[$option->getY()][$option->getX()][] = $option;
    }

    public function getWidth()
    {
        return $this->mapWidth;
    }

    public function getHeight()
    {
        return $this->mapHeight;
    }
}