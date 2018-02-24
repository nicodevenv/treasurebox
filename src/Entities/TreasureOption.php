<?php
namespace App\Entities;

class TreasureOption extends AbstractOption {
    private $counter;

    /**
     * TreasureOption constructor.
     * @param $data
     * @param Map $map
     * @throws \Exception
     */
    public function __construct($data, Map $map)
    {
        $this->counter = $data['counter'];

        parent::__construct($data, $map);
    }

    public function getType()
    {
        return 'Treasure';
    }
}