<?php
namespace App\Service;

use App\Entities\AdventurerOption;
use App\Entities\TreasureOption;

class AdventurerService
{
    /**
     * @throws \Exception
     */
    public function addTreasure(AdventurerOption $adventurer)
    {
        $foundTreasure = null;
        foreach ($adventurer->getTreasures() as $treasure) {
            /** @var TreasureOption $treasure */
            if ($treasure->getX() === $adventurer->getX() && $treasure->getY() === $adventurer->getY()) {
                $foundTreasure = $treasure;
            }
        }

        if ($foundTreasure === null) {
            $treasureData = [
                'x'       => $adventurer->getX(),
                'y'       => $adventurer->getY(),
                'counter' => 1,
            ];

            $foundTreasure = new TreasureOption($treasureData);
            $adventurer->addTreasure($foundTreasure);

            return;
        }

        /** @var TreasureOption $foundTreasure */
        $foundTreasure->incrementCounter();
    }

    public function getNextPosition(AdventurerOption $adventurer)
    {
        $x = $adventurer->getX();
        $y = $adventurer->getY();

        switch ($adventurer->getDirection()) {
            case AdventurerOption::NORTH_DIRECTION:
                $y -= 1;
                break;
            case AdventurerOption::SOUTH_DIRECTION:
                $y += 1;
                break;
            case AdventurerOption::EAST_DIRECTION:
                $x += 1;
                break;
            case AdventurerOption::WEST_DIRECTION:
                $x -= 1;
                break;
        }

        return [
            'x' => $x,
            'y' => $y,
        ];
    }

    public function move(AdventurerOption $adventurer)
    {
        $nextPosition = $this->getNextPosition($adventurer);
        $adventurer->setX($nextPosition['x']);
        $adventurer->setY($nextPosition['y']);
    }

    public function turn(AdventurerOption $adventurer, $turnTo)
    {
        $adventurer->setDirection($this->getNextDirection($adventurer, $turnTo));
    }

    public function getNextDirection(AdventurerOption $adventurer, $turnTo, $selectFirst = false)
    {
        $sortedDirections = AdventurerOption::SORTED_DIRECTIONS;
        if ($turnTo === 'G') {
            $sortedDirections = array_reverse($sortedDirections);
        }

        $selectNext = false;
        if ($selectFirst) {
            $selectNext = true;
        }

        foreach ($sortedDirections as $direction) {
            if ($selectNext) {
                return $direction;
            }

            if ($direction === $adventurer->getDirection()) {
                $selectNext = true;
            }
        }

        return $this->getNextDirection($adventurer, $turnTo, true);
    }
}