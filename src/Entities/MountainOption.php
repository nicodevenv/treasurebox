<?php

namespace App\Entities;

class MountainOption extends AbstractOption
{
    const REFERENCE = 'M';

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'Mountain';
    }
}