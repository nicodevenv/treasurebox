<?php

namespace App\Entities;

class MountainOption extends AbstractOption {
    /**
     * MountainOption constructor.
     * @param $data
     * @param Map $map
     * @throws \Exception
     */
    public function __construct($data, Map $map)
    {
        parent::__construct($data, $map);
    }

    public function getType()
    {
        return 'Mountain';
    }
}