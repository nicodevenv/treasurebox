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

    public function getWidth()
    {
        return $this->mapWidth;
    }

    public function getHeight()
    {
        return $this->mapHeight;
    }
}