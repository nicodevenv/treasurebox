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

    public function getLongestCharCount($options)
    {
        $charCount = 0;
        $reservedCharCount = 4;

        foreach ($options as $option) {
            $currentCharCount = 0;

            if ($option instanceof TreasureOption) {
                $currentCharCount = strlen($option->getCounter());
            }
            if ($option instanceof AdventurerOption) {
                $currentCharCount = strlen($option->getName());
            }

            $currentCharCount += $reservedCharCount;
            if ($currentCharCount > $charCount) {
                $charCount = $currentCharCount;
            }
        }

        return $charCount;
    }

    public function displayMap($longestCharCount)
    {
        $outputStr = '';
        foreach ($this->mapFrames as $y => $row) {
            foreach($row as $x => $data) {
                $currentStr = '.';
                if (count($data) > 0) {
                    foreach ($data as $option) {
                        if ($option instanceof MountainOption) {
                            $currentStr = MountainOption::REFERENCE;
                        }

                        if ($option instanceof TreasureOption) {
                            $currentStr = sprintf(
                                '%s(%s)',
                                TreasureOption::REFERENCE,
                                $option->getCounter()
                            );
                        }

                        if ($option instanceof AdventurerOption) {
                            $currentStr = sprintf(
                                '%s(%s)',
                                AdventurerOption::REFERENCE,
                                $option->getName()
                            );
                        }
                    }
                }

                $missingBlankSpace = $longestCharCount - strlen($currentStr);
                for ($i = 0; $i <= $missingBlankSpace; $i++) {
                    $currentStr .= ' ';
                }

                $outputStr .= $currentStr;
            }
            $outputStr .= "\n";
        }

        return $outputStr;
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

        if ($isStackable && $this->isMountainHere($option->getX(), $option->getY())) {
            throw new \Exception(
                sprintf(
                    'Unable to put option in frame [%s, %s] because there is a mountain here.',
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

    public function isAdventurerMovable(AdventurerOption $adventurer)
    {
        $nextPosition = $adventurer->getNextPosition();
        if (
            !CheckerService::isIntegerAndMoreThanZero(true, $nextPosition['x'], $this->getMaxX())
            || !CheckerService::isIntegerAndMoreThanZero(true, $nextPosition['y'], $this->getMaxY())
            || $this->isMountainHere($nextPosition['x'], $nextPosition['y'])
        ) {
            return false;
        }

        return true;
    }

    public function isMountainHere($x, $y) {
        foreach ($this->mapFrames[$y][$x] as $option) {
            if ($option instanceof MountainOption) {
                return true;
            }
        }

        return false;
    }

    public function removeOption(AbstractOption $option)
    {
        foreach ($this->mapFrames[$option->getY()][$option->getX()] as $index => $loopOption) {
            if ($option === $loopOption) {
                unset($this->mapFrames[$option->getY()][$option->getX()][$index]);
            }
        }

    }

    /**
     * @param AdventurerOption $adventurer
     * @throws \Exception
     */
    public function moveAdventurer(AdventurerOption $adventurer)
    {
        $this->removeOption($adventurer);
        $adventurer->move();
        $this->addOption($adventurer, true);

        $this->collectSomething($adventurer);
    }

    /**
     * @param AdventurerOption $adventurer
     * @throws \Exception
     */
    private function collectSomething(AdventurerOption $adventurer)
    {
        if (count($this->mapFrames[$adventurer->getY()][$adventurer->getX()]) > 0) {
            foreach ($this->mapFrames[$adventurer->getY()][$adventurer->getX()] as $optionIndex => $option) {
                if ($option instanceof TreasureOption) {
                    /** @var TreasureOption $option */
                    $adventurer->addTreasure();
                    $option->decrementCounter();
                    if ($option->getCounter() === 0) {
                        unset($this->mapFrames[$adventurer->getY()][$adventurer->getX()][$optionIndex]);
                    }
                }
            }
        }
    }
}