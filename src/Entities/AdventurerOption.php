<?php

namespace  App\Entities;

use App\Service\CheckerService;

class AdventurerOption extends AbstractOption {
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
     * @param $data
     * @param Map $map
     * @throws \Exception
     */
    public function __construct($data, Map $map)
    {
        $name = $data['name'];
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
        $this->actions = $actionSet;
        $this->name = $name;

        parent::__construct($data, $map);
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTreasures()
    {
        return $this->treasures;
    }

    public  function getType()
    {
        return 'Adventurer';
    }

    public function addTreasure(TreasureOption $treasure)
    {
        $this->treasures[] = $treasure;
    }

    public function setDirection($direction)
    {
        $this->direction = $direction;
    }
}