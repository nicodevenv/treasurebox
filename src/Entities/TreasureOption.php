<?php
namespace App\Entities;

class TreasureOption extends AbstractOption
{
    const REFERENCE = 'T';

    private $counter;

    /**
     * TreasureOption constructor.
     *
     * @param     $data
     * @param Map $map
     *
     * @throws \Exception
     */
    public function __construct($data, Map $map = null)
    {
        $this->counter = $data['counter'];

        parent::__construct($data, $map);
    }

    public function incrementCounter()
    {
        $this->counter++;
    }

    public function decrementCounter()
    {
        $this->counter--;
    }

    public function getCounter()
    {
        return $this->counter;
    }

    public function getType()
    {
        return 'Treasure';
    }
}