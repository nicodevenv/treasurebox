<?php

namespace App\Entities;

use App\Service\CheckerService;

class AdventurerOption extends AbstractOption
{
    const REFERENCE = 'A';

    const NORTH_DIRECTION = 'N';
    const SOUTH_DIRECTION = 'S';
    const EAST_DIRECTION = 'E';
    const WEST_DIRECTION = 'O';
    const SORTED_DIRECTIONS = [self::NORTH_DIRECTION, self::EAST_DIRECTION, self::SOUTH_DIRECTION, self::WEST_DIRECTION];
    const MOVE_ACTION = 'A';
    const LEFT_ACTION = 'G';
    const RIGHT_ACTION = 'D';

    private $direction = '';

    private $actions = '';

    private $name = '';

    private $treasures = [];

    /**
     * AdventurerOption constructor.
     *
     * @param     $data
     * @param Map $map
     *
     * @throws \Exception
     */
    public function __construct($data, Map $map)
    {
        $name      = $data['name'];
        $direction = $data['direction'];
        $actionSet = $data['actions'];

        if (!CheckerService::isValidAdventurerDirection($direction)) {
            throw new \Exception(
                sprintf(
                    'Unable to set adventurer direction. Allowed values are [%s], "%s" given.',
                    implode(', ', CheckerService::$allowedDirections),
                    $direction
                )
            );
        }

        if (!CheckerService::isValidAdventurerActionSet($actionSet)) {
            throw new \Exception(
                sprintf(
                    'One or more actions value are not valid for adventurer %s. Allowed values [%s], "%s" given',
                    $name,
                    implode(', ', CheckerService::$allowedActions),
                    $actionSet
                )
            );
        }

        $this->direction = $direction;
        $this->actions   = $actionSet;
        $this->name      = $name;

        parent::__construct($data, $map);
    }

    /**
     * @return string
     */
    public function getActions(): string
    {
        return $this->actions;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getTreasures(): array
    {
        return $this->treasures;
    }

    /**
     * @param TreasureOption $treasure
     *
     * @return $this
     */
    public function addTreasure(TreasureOption $treasure)
    {
        $this->treasures[] = $treasure;

        return $this;
    }

    /**
     * @param $direction
     *
     * @return $this
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'Adventurer';
    }
}