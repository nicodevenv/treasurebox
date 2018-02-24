<?php

namespace App\Entities;

class MountainOption extends AbstractOption {
    const REFERENCE = 'M';

    public function getType()
    {
        return 'Mountain';
    }
}