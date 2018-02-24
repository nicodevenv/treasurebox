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
    public function __construct($data, Map $map = null)
    {
        $maxX = null;
        $maxY = null;
        if ($map instanceof Map) {
            $maxX = $map->getMaxX();
            $maxY = $map->getMaxY();
        }

        if (!CheckerService::isIntegerAndMoreThanZero(true, $data['x'], $maxX)) {
            throw new \Exception($this->getType() . ' x is invalid. Attempted for integer > 0 < ' . $maxX);
        }

        if (!CheckerService::isIntegerAndMoreThanZero(true, $data['y'], $maxY)) {
            throw new \Exception($this->getType() . ' y is invalid. Attempted for integer > 0 < ' . $maxY);
        }

        $this->x = intval($data['x']);
        $this->y = intval($data['y']);
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function setX($x)
    {
        $this->x = $x;
    }

    public function setY($y)
    {
        $this->y = $y;
    }

    public abstract function getType();
}