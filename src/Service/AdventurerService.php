<?php
namespace App\Service;

use App\Entities\AdventurerOption;
use App\Entities\TreasureOption;

class AdventurerService
{
    /**
     * @param AdventurerOption $adventurer
     * @param TreasureOption   $treasureToAdd
     *
     * @throws \Exception
     */
    public function addTreasure(AdventurerOption $adventurer, TreasureOption $treasureToAdd)
    {
        if ($adventurer->getX() !== $treasureToAdd->getX() || $adventurer->getY() !== $treasureToAdd->getY()) {
            throw new \Exception('Adventurer does not have the same position as Treasure\'s');
        }

        $foundTreasure = null;
        foreach ($adventurer->getTreasures() as $treasure) {
            /** @var TreasureOption $treasure */
            if ($treasure->getX() === $treasureToAdd->getX() && $treasure->getY() === $treasureToAdd->getY()) {
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

    /**
     * @param AdventurerOption $adventurer
     *
     * @return array
     */
    public function getNextPosition(AdventurerOption $adventurer): array
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

    /**
     * @param AdventurerOption $adventurer
     */
    public function move(AdventurerOption $adventurer)
    {
        $nextPosition = $this->getNextPosition($adventurer);
        $adventurer->setX($nextPosition['x']);
        $adventurer->setY($nextPosition['y']);
    }

    /**
     * @param AdventurerOption $adventurer
     * @param string           $turnTo
     * @param bool             $selectFirst
     *
     * @return string
     */
    public function getNextDirection(AdventurerOption $adventurer, string $turnTo, bool $selectFirst = false): string
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

    /**
     * @param AdventurerOption $adventurer
     * @param string           $turnTo
     */
    public function turn(AdventurerOption $adventurer, string $turnTo)
    {
        $adventurer->setDirection($this->getNextDirection($adventurer, $turnTo));
    }
}