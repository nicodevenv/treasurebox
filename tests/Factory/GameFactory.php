<?php

namespace App\Tests\Factory;

use App\Service\AdventurerService;
use App\Service\GameService;
use App\Service\MapService;

class GameFactory
{
    private $data = [];

    private $gameService;

    /**
     * GameFactory constructor.
     *
     * @param                   $data
     * @param MapService        $mapService
     * @param AdventurerService $adventurerService
     */
    public function __construct($data, MapService $mapService, AdventurerService $adventurerService)
    {
        $this->data = $data;

        $this->gameService = new GameService($mapService, $adventurerService);
        $this->gameService->setConfigurationPath('tests/Files/game_configuration_test.txt');
        $this->gameService->setOutputPath('tests/Files/game_output_test.txt');

        $this->gameService->generateConfigurationFromArray($this->data);
    }

    /**
     * @return GameService
     */
    public function getGameService(): GameService
    {
        return $this->gameService;
    }
}