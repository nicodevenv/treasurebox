<?php
namespace App\Service;

use App\Entities;

class GameService
{
    const STEP_SEPARATOR = "------------------------------------\n";

    private $configurationPath = 'public/inputs/game_configuration.txt';
    private $outputPath = 'public/outputs/game_output.txt';

    private $allAdventurers = [];
    private $allTreasures = [];
    private $allMountains = [];

    private $optionTypes = [
        Entities\MountainOption::REFERENCE,
        Entities\TreasureOption::REFERENCE,
        Entities\AdventurerOption::REFERENCE
    ];

    /** @var MapService */
    private $mapService;

    /** @var AdventurerService */
    private $adventurerService;

    /** @var Entities\Map */
    private $map;

    public function __construct(MapService $mapService, AdventurerService $adventurerService)
    {
        $this->mapService        = $mapService;
        $this->adventurerService = $adventurerService;
    }

    public function getConfigurationPath()
    {
        return $this->configurationPath;
    }

    public function setConfigurationPath($path)
    {
        $this->configurationPath = $path;
    }

    public function getOutputPath()
    {
        return $this->outputPath;
    }

    public function setOutputPath($path)
    {
        $this->outputPath = $path;
    }

    public function generateConfigurationFromArray($data)
    {
        $file = fopen($this->configurationPath, 'w');

        foreach ($data as $row) {
            fwrite($file, $row . "\n");
        }

        fclose($file);
    }

    /**
     * @throws \Exception
     */
    private function getFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception('The current file does not exist : ' . $filePath);
        }

        $file = fopen($filePath, 'r');

        if (!$file) {
            throw new \Exception('Unable to open file : ' . $filePath);
        }

        return $file;
    }

    /**
     * @param $dataArray
     * @param $count
     *
     * @return string
     * @throws \Exception
     */
    private function checkExactDataNumber($dataArray, $count)
    {
        if (count($dataArray) > $count) {
            throw new \Exception('The current data are not valid : ' . implode(' - ', $dataArray));
        }

        return '';
    }

    /**
     * @throws \Exception
     */
    public function prepareGameConfiguration()
    {
        $sortedData = $this->sortGameConfigurationData();

        foreach ($sortedData as $type => $data) {
            //For Mountains, Treasures, Adventurers, there is a sub array to loop
            if (in_array($type, $this->optionTypes)) {
                foreach ($data as $row) {
                    $this->createEntities($type, $row);
                }
                continue;
            }

            //For Map data only, we can get the values directly
            $this->createEntities($type, $data);
        }
    }

    /**
     * @throws \Exception
     */
    private function sortGameConfigurationData()
    {
        $file = $this->getFile($this->configurationPath);

        $mapDimension = [];
        $mountains    = [];
        $treasures    = [];
        $adventurers  = [];

        $separator = ' - ';
        while (($row = fgets($file)) !== false) {
            $row = trim($row);
            if (strlen($row) > 0) {
                $contentArray = explode($separator, $row);

                //The first char of each row represent it's type [C, M, T, A]
                $type = $contentArray[0];

                switch ($type) {
                    case Entities\Map::REFERENCE:
                        //Check that the map dimensions are already defined
                        if (!empty($mapDimension)) {
                            throw new \Exception('Map dimensions are defined multiple times');
                        }

                        //Ensure that the row contains 3 columns of data [Type - X - Y]
                        $this->checkExactDataNumber($contentArray, 3);
                        $mapDimension = [
                            'width'  => $contentArray[1],
                            'height' => $contentArray[2],
                        ];
                        break;
                    case Entities\MountainOption::REFERENCE:
                        //Ensure that the row contains 3 columns of data [Type - X - Y]
                        $this->checkExactDataNumber($contentArray, 3);

                        $mountains[] = [
                            'x' => $contentArray[1],
                            'y' => $contentArray[2],
                        ];
                        break;
                    case Entities\TreasureOption::REFERENCE:
                        //Ensure that the row contains 4 columns of data [Type - X - Y - COUNTER]
                        $this->checkExactDataNumber($contentArray, 4);

                        $treasures[] = [
                            'x'       => $contentArray[1],
                            'y'       => $contentArray[2],
                            'counter' => $contentArray[3],
                        ];
                        break;
                    case Entities\AdventurerOption::REFERENCE:
                        //Ensure that the row contains 6 columns of data [Type - NAME - X - Y - DIRECTION - ACTIONS]
                        $this->checkExactDataNumber($contentArray, 6);

                        $adventurers[] = [
                            'name'      => $contentArray[1],
                            'x'         => $contentArray[2],
                            'y'         => $contentArray[3],
                            'direction' => $contentArray[4],
                            'actions'   => $contentArray[5],
                        ];
                        break;
                    default:
                        throw new \Exception(
                            sprintf(
                                'Type format not allowed. "%s" given. ' .
                                'Warning ! We\'ve detected some problems with copy paste data.. We\'ll fix it ASAP',
                                $type
                            )
                        );
                        break;
                }
            }
        }

        return [
            Entities\Map::REFERENCE              => $mapDimension,
            Entities\MountainOption::REFERENCE   => $mountains,
            Entities\TreasureOption::REFERENCE   => $treasures,
            Entities\AdventurerOption::REFERENCE => $adventurers,
        ];
    }

    /**
     * @param $type
     * @param $data
     *
     * @throws \Exception
     */
    private function createEntities($type, $data)
    {
        $option = null;

        switch ($type) {
            case Entities\Map::REFERENCE:
                $this->map = new Entities\Map($data['width'], $data['height'], $this->outputPath);
                break;
            case Entities\MountainOption::REFERENCE:
                $option               = new Entities\MountainOption($data, $this->map);
                $this->allMountains[] = $option;
                break;
            case Entities\TreasureOption::REFERENCE:
                $option               = new Entities\TreasureOption($data, $this->map);
                $this->allTreasures[] = $option;
                break;
            case Entities\AdventurerOption::REFERENCE:
                $option                 = new Entities\AdventurerOption($data, $this->map);
                $this->allAdventurers[] = $option;
                break;
            default:
                throw new \Exception(
                    sprintf(
                        'Unable to create entity. (case not implemented : %s)',
                        $type
                    )
                );
                break;
        }

        if ($option !== null) {
            $this->mapService->addOption($this->map, $option);
        }
    }

    public function getMap()
    {
        return $this->map;
    }

    /**
     * @throws \Exception
     */
    public function getGameSteps($displayOnMove = false)
    {
        $gameSteps = '';

        $maxLoop = 1; // Save the max actions of aventurer
        $looping = 0; // Used to get the [index] action of adventurer

        // Get the longest char number to write in result (add blank space for difference)
        $longestCharCount = $this->mapService->getLongestCharCount(array_merge($this->allTreasures, $this->allAdventurers));

        // Start result
        $gameSteps .= $this->mapService->displayMap($this->map, $longestCharCount);
        $gameSteps .= self::STEP_SEPARATOR;

        // Looping on each adventurer / each action 1 by 1
        while ($looping < $maxLoop) {
            foreach ($this->allAdventurers as $adventurer) {
                /** @var Entities\AdventurerOption $adventurer */
                $actions = $adventurer->getActions();

                // Save if found another longest char count to continue [while] instruction
                if (strlen($actions) > $maxLoop) {
                    $maxLoop = strlen($actions);
                }

                if (isset($actions[$looping])) {
                    switch ($actions[$looping]) {
                        case Entities\AdventurerOption::MOVE_ACTION:
                            if ($this->mapService->isAdventurerMovable($this->map, $adventurer)) {
                                $this->mapService->moveAdventurer($this->map, $adventurer);
                                // Display each step if chosen in play command
                                if ($displayOnMove) {
                                    $gameSteps .= $this->mapService->displayMap($this->map, $longestCharCount);
                                    // Do not add separator if the current step is last
                                    if ($looping < $maxLoop) {
                                        $gameSteps .= self::STEP_SEPARATOR;
                                    }
                                }
                            }
                            break;
                        case Entities\AdventurerOption::RIGHT_ACTION;
                            $this->adventurerService->turn($adventurer, Entities\AdventurerOption::RIGHT_ACTION);
                            break;
                        case Entities\AdventurerOption::LEFT_ACTION:
                            $this->adventurerService->turn($adventurer, Entities\AdventurerOption::LEFT_ACTION);
                            break;
                    }
                }
            }
            $looping++;
        }

        // Display last step if each step displayer is deactivated (prevent duplication of data)
        if (!$displayOnMove) {
            $gameSteps .= $this->mapService->displayMap($this->map, $longestCharCount);
        }

        return $gameSteps;
    }

    public function writeResults()
    {
        $file = fopen($this->outputPath, 'w');

        $mapStr = sprintf('C - %s - %s', $this->map->getWidth(), $this->map->getHeight());
        fwrite($file, $mapStr . "\n");

        foreach ($this->allMountains as $mountain) {
            /** @var Entities\MountainOption $mountain */
            $mountainStr = sprintf('M - %s - %s', $mountain->getX(), $mountain->getY());
            fwrite($file, $mountainStr . "\n");
        }

        foreach ($this->allTreasures as $treasure) {
            /** @var Entities\TreasureOption $treasure */
            if ($treasure->getCounter() > 0) {
                $treasureStr = sprintf('T - %s - %s - %s', $treasure->getX(), $treasure->getY(), $treasure->getCounter());
                fwrite($file, $treasureStr . "\n");
            }
        }

        foreach ($this->allAdventurers as $adventurer) {
            /** @var Entities\AdventurerOption $adventurer */
            $treasureCollected = 0;

            if (count($adventurer->getTreasures()) > 0) {
                foreach ($adventurer->getTreasures() as $treasure) {
                    $treasureCollected += $treasure->getCounter();
                }
            }

            $adventurerStr = sprintf(
                'A - %s - %s - %s - %s - %s',
                $adventurer->getName(),
                $adventurer->getX(),
                $adventurer->getY(),
                $adventurer->getDirection(),
                $treasureCollected
            );
            fwrite($file, $adventurerStr . "\n");
        }

        fclose($file);
    }
}
