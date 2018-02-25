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

    private $gameService;

    public function __construct()
    {
        $this->gameService = new GameService();
        $this->gameService->setConfigurationPath('tests/Files/game_configuration_test.txt');
        $this->gameService->setOutputPath('tests/Files/game_output_test.txt');

        $this->gameService->generateConfigurationFromArray($this->data);
    }

    public function getGameService()
    {
        return $this->gameService;
    }
}