<?php

namespace App\Entities;

use App\Service\CheckerService;

abstract class AbstractOption
{
    private $x;

    private $y;

    /**
     * AbstractOption constructor.
     *
     * @param          $data
     * @param Map|null $map
     *
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

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param $x
     *
     * @return $this
     */
    public function setX($x)
    {
        $this->x = $x;

        return $this;
    }


    /**
     * @param $y
     *
     * @return $this
     */
    public function setY($y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * @return string
     */
    public abstract function getType(): string;
}