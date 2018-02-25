<?php

namespace App\Service;

use App\Entities\AbstractOption;
use App\Entities\AdventurerOption;
use App\Entities\Map;
use App\Entities\MountainOption;
use App\Entities\TreasureOption;

class MapService
{
    private $adventurerService;

    public function __construct(AdventurerService $adventurerService)
    {
        $this->adventurerService = $adventurerService;
    }

    public function isAdventurerMovable(Map $map, AdventurerOption $adventurer)
    {
        $nextPosition = $this->adventurerService->getNextPosition($adventurer);
        if (
            !CheckerService::isIntegerAndMoreThanZero(true, $nextPosition['x'], $map->getMaxX())
            || !CheckerService::isIntegerAndMoreThanZero(true, $nextPosition['y'], $map->getMaxY())
            || $this->isObstacleHere($map, $nextPosition['x'], $nextPosition['y'])
        ) {
            return false;
        }

        return true;
    }

    public function isObstacleHere(Map $map, $x, $y)
    {
        foreach ($map->getMapFrames()[$y][$x] as $option) {
            if ($option instanceof MountainOption || $option instanceof AdventurerOption) {
                return true;
            }
        }

        return false;
    }

    public function removeOption(Map $map, AbstractOption $option)
    {
        foreach ($map->getMapFrames()[$option->getY()][$option->getX()] as $index => $loopOption) {
            if ($option === $loopOption) {
                $map->removeOption($option, $index);
            }
        }

    }

    /**
     * @param AdventurerOption $adventurer
     *
     * @throws \Exception
     */
    public function moveAdventurer(Map $map, AdventurerOption $adventurer)
    {
        $this->removeOption($map, $adventurer);
        $this->adventurerService->move($adventurer);
        $this->addOption($map, $adventurer, true);

        $this->collectSomething($map, $adventurer);
    }

    /**
     * @param AdventurerOption $adventurer
     *
     * @throws \Exception
     */
    private function collectSomething(Map $map, AdventurerOption $adventurer)
    {
        if (count($map->getMapFrames()[$adventurer->getY()][$adventurer->getX()]) > 0) {
            foreach ($map->getMapFrames()[$adventurer->getY()][$adventurer->getX()] as $optionIndex => $option) {
                if ($option instanceof TreasureOption) {
                    /** @var TreasureOption $option */
                    $this->adventurerService->addTreasure($adventurer, $option);
                    $option->decrementCounter();
                    if ($option->getCounter() === 0) {
                        $map->removeOption($adventurer, $optionIndex);
                    }
                }
            }
        }
    }

    public function getLongestCharCount($options)
    {
        $charCount         = 0;
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

    public function displayMap(Map $map, $longestCharCount)
    {
        $outputStr = '';
        foreach ($map->getMapFrames() as $y => $row) {
            foreach ($row as $x => $data) {
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
     * @param bool           $isStackable
     *
     * @throws \Exception
     */
    public function addOption(Map $map, $option, $isStackable = false)
    {
        if (!$isStackable && count($map->getMapFrames()[$option->getY()][$option->getX()]) > 0) {
            throw new \Exception(
                sprintf(
                    'Unable to add %s.. another option is already using [%s,%s] position',
                    $option->getType(),
                    $option->getX(),
                    $option->getY()
                )
            );
        }

        if ($isStackable && $this->isObstacleHere($map, $option->getX(), $option->getY())) {
            throw new \Exception(
                sprintf(
                    'Unable to put option in frame [%s, %s] because there is a mountain here.',
                    $option->getX(),
                    $option->getY()
                )
            );
        }

        $map->addOption($option);
    }
}