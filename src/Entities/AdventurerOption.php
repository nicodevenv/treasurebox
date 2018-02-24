<?php

namespace  App\Entities;

use App\Service\CheckerService;

class AdventurerOption extends AbstractOption {
    private $direction;

    private $actions;

    private $name;

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

    public function addTreasure(TreasureOption $treasure)
    {
        $this->treasures[] = $treasure;
    }

    public  function getType()
    {
        return 'Adventurer';
    }
}