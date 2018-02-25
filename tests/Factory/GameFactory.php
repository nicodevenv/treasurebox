<?php

namespace App\Tests\Factory;

use App\Service\GameService;

class GameFactory {
    private $data = [
        'C - 3 - 4',
        'M - 1 - 0',
        'M - 2 - 1',
        'T - 0 - 3 - 2',
        'T - 1 - 3 - 3',
        'A - Lara - 1 - 1 - S - AADADAGGA',
    ];

    public function __construct()
    {
        $gameService = new GameService();

        $gameService->generateConfigurationFromArray($this->data);
    }
}