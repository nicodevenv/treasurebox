<?php

namespace App\Tests\Factory;

use App\Service\GameService;

class GameFactory
{
    private $data = [];

    private $gameService;

    public function __construct($data)
    {
        $this->data = $data;

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