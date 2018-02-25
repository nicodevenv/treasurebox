<?php

namespace App\Tests\Factory;

use App\Service\AdventurerService;
use App\Service\GameService;
use App\Service\MapService;

class GameFactory
{
    private $data = [];

    private $gameService;

    public function __construct($data, MapService $mapService, AdventurerService $adventurerService)
    {
        $this->data = $data;

        $this->gameService = new GameService($mapService, $adventurerService);
        $this->gameService->setConfigurationPath('tests/Files/game_configuration_test.txt');
        $this->gameService->setOutputPath('tests/Files/game_output_test.txt');

        $this->gameService->generateConfigurationFromArray($this->data);
    }

    public function getGameService()
    {
        return $this->gameService;
    }
}