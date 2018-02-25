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

    /**
     * @return $this
     */
    public function incrementCounter()
    {
        $this->counter++;

        return $this;
    }

    /**
     * @return $this
     */
    public function decrementCounter()
    {
        $this->counter--;

        return $this;
    }

    /**
     * @return int
     */
    public function getCounter(): int
    {
        return $this->counter;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'Treasure';
    }
}