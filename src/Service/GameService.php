<?php
namespace App\Service;

use App\Entities;

class GameService {
    const CONFIGURATION_PATH = 'public/inputs/game_configuration.txt';
    const OUTPUT_PATH = 'public/outputs/game_output.txt';

    private $allAdventurers = [];
    private $allTreasures = [];
    private $allMountains = [];

    private $optionTypes = [
        Entities\MountainOption::REFERENCE,
        Entities\TreasureOption::REFERENCE,
        Entities\AdventurerOption::REFERENCE
    ];

    /** @var Entities\Map */
    private $map;

    public function generateConfigurationFromArray($data)
    {
        $file = fopen(self::CONFIGURATION_PATH, 'w');

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
        if (!file_exists(self::CONFIGURATION_PATH)) {
            throw new \Exception('The current file does not exist : ' . $filePath);
        }

        $file = fopen(self::CONFIGURATION_PATH, 'r');

        if (!$file) {
            throw new \Exception('Unable to open file : ' . $filePath);
        }

        return $file;
    }

    /**
     * @param $dataArray
     * @param $count
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
    private function sortGameConfigurationData()
    {
        $file = $this->getFile(self::CONFIGURATION_PATH);

        $mapDimension = [];
        $mountains = [];
        $treasures = [];
        $adventurers = [];

        $separator = ' - ';
        while (($row = fgets($file)) !== false) {
            $row = trim($row);
            if (strlen($row) > 0) {
                $contentArray = explode($separator, $row);
                $type = $contentArray[0];

                switch($type) {
                    case Entities\Map::REFERENCE:
                        if (!empty($mapDimension)) {
                            throw new \Exception('Map dimensions are defined multiple times');
                        }

                        $this->checkExactDataNumber($contentArray, 3);
                        $mapDimension = [
                            'width' => $contentArray[1],
                            'height' => $contentArray[2],
                        ];
                        break;
                    case Entities\MountainOption::REFERENCE:
                        $this->checkExactDataNumber($contentArray, 3);

                        $mountains[] = [
                            'x' => $contentArray[1],
                            'y' => $contentArray[2],
                        ];
                        break;
                    case Entities\TreasureOption::REFERENCE:
                        $this->checkExactDataNumber($contentArray, 4);

                        $treasures[] = [
                            'x' => $contentArray[1],
                            'y' => $contentArray[2],
                            'counter' => $contentArray[3],
                        ];
                        break;
                    case Entities\AdventurerOption::REFERENCE:
                        $this->checkExactDataNumber($contentArray, 6);
                        $adventurers[] = [
                            'name' => $contentArray[1],
                            'x' => $contentArray[2],
                            'y'=> $contentArray[3],
                            'direction' => $contentArray[4],
                            'actions' => $contentArray[5],
                        ];
                        break;
                    default:
                        throw new \Exception(
                            sprintf(
                                'Type format not allowed. "%s" given. ' .
                                'Warning ! We\'ve detected some problem with copy paste data.. We\'ll fix it ASAP',
                                $type
                            )
                        );
                        break;
                }
            }
        }

        return [
            Entities\Map::REFERENCE => $mapDimension,
            Entities\MountainOption::REFERENCE => $mountains,
            Entities\TreasureOption::REFERENCE => $treasures,
            Entities\AdventurerOption::REFERENCE => $adventurers,
        ];
    }

    /**
     * @throws \Exception
     */
    public function prepareGameConfiguration()
    {
        $sortedData = $this->sortGameConfigurationData();

        foreach ($sortedData as $type => $data) {
            if (in_array($type, $this->optionTypes)) {
                foreach ($data as $row) {
                    $this->createEntities($type, $row);
                }
                continue;
            }

            $this->createEntities($type, $data);
        }
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

        switch($type) {
            case Entities\Map::REFERENCE:
                $this->map = new Entities\Map($data['width'], $data['height'], self::OUTPUT_PATH);
                break;
            case Entities\MountainOption::REFERENCE:
                $option = new Entities\MountainOption($data, $this->map);
                $this->allMountains[] = $option;
                break;
            case Entities\TreasureOption::REFERENCE:
                $option = new Entities\TreasureOption($data, $this->map);
                $this->allTreasures[] = $option;
                break;
            case Entities\AdventurerOption::REFERENCE:
                $option = new Entities\AdventurerOption($data, $this->map);
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
            $this->map->addOption($option);
        }
    }

    public function getMap()
    {
        return $this->map;
    }

    /**
     * @throws \Exception
     */
    public function playGameConfiguration($displayOnMove = false)
    {
        $maxLoop = 1;
        $looping = 0;

        $longestCharCount = $this->map->getLongestCharCount(array_merge($this->allTreasures, $this->allAdventurers));
        $separator = "------------------------------------\n";

        echo $this->map->displayMap($longestCharCount);
        echo $separator;

        while($looping < $maxLoop) {
            foreach( $this->allAdventurers as $adventurer) {
                /** @var Entities\AdventurerOption $adventurer */
                $actions = $adventurer->getActions();

                if (strlen($actions) > $maxLoop) {
                    $maxLoop = strlen($actions);
                }

                if (isset($actions[$looping])) {
                    switch ($actions[$looping]) {
                        case Entities\AdventurerOption::MOVE_ACTION:
                            if ($this->map->isAdventurerMovable($adventurer)) {
                                $this->map->moveAdventurer($adventurer);
                                if ($displayOnMove) {
                                    echo $this->map->displayMap($longestCharCount);
                                    echo $separator;
                                }
                            }
                            break;
                        case Entities\AdventurerOption::RIGHT_ACTION;
                            $adventurer->turn(Entities\AdventurerOption::RIGHT_ACTION);
                            break;
                        case Entities\AdventurerOption::LEFT_ACTION:
                            $adventurer->turn(Entities\AdventurerOption::LEFT_ACTION);
                            break;
                    }
                }
            }
            $looping++;
        }
    }

    public function writeResults()
    {
        $file = fopen(self::OUTPUT_PATH, 'w');

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
