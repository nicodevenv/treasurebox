<?php

namespace App\Entities;

use App\Service\CheckerService;

abstract class AbstractOption {
    private $x;

    private $y;

    /**
     * AbstractOption constructor.
     * @param $data
     * @param Map $map
     * @throws \Exception
     */
    public function __construct($data, Map $map)
    {
        $maxX = $map->getWidth()-1;
        $maxY = $map->getHeight()-1;

        if (!CheckerService::isIntegerAndMoreThanZero(true, $data['x'], $maxX)) {
            throw new \Exception($this->getType() . ' x is invalid. Attempted for integer > 0 < ' . $maxX);
        }

        if (!CheckerService::isIntegerAndMoreThanZero(true, $data['y'], $maxY)) {
            throw new \Exception($this->getType() . ' y is invalid. Attempted for integer > 0 < ' . $maxY);
        }

        $this->x = $data['x'];
        $this->y = $data['y'];
    }

    public abstract function getType();
}