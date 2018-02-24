<?php
namespace App\Service;

use App\Entities;

class GameService {
    const MAP_REFERENCE = 'C';
    const TREASURE_REFERENCE = 'T';
    const MOUNTAIN_REFERENCE = 'M';
    const ADVENTURER_REFERENCE = 'A';

    private $gameConfigurationPath = 'public/inputs/game_configuration.txt';

    private $gameOutputPath = 'public/output/game_output.txt';

    private $sortedAdventurers = [];

    private $optionTypes = [self::MOUNTAIN_REFERENCE, self::TREASURE_REFERENCE, self::ADVENTURER_REFERENCE];

    /** @var Entities\Map */
    private $map;

    public function generateConfigurationFromArray($data)
    {
        $filePath = $this->gameConfigurationPath;

        $file = fopen($filePath, 'w');

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
        if (!file_exists($this->gameConfigurationPath)) {
            throw new \Exception('The current file does not exist : ' . $filePath);
        }

        $file = fopen($this->gameConfigurationPath, 'r');

        if (!$file) {
            throw new \Exception('Unable to open file : ' . $filePath);
        }

        return $file;
    }

    /**
     * @param $dataArray
     * @param $count
     *
     * @throws \Exception
     */
    private function checkExactDataNumber($dataArray, $count)
    {
        if (count($dataArray) > $count) {
            throw new \Exception('The current data are not valid : ' . implode(' - ', $dataArray));
        }
    }

    /**
     * @throws \Exception
     */
    private function sortGameConfigurationData()
    {
        $file = $this->getFile($this->gameConfigurationPath);

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
                    case self::MAP_REFERENCE:
                        if (!empty($mapDimension)) {
                            throw new \Exception('Map dimensions are defined multiple times');
                        }

                        $this->checkExactDataNumber($contentArray, 3);
                        $mapDimension = [
                            'width' => $contentArray[1],
                            'height' => $contentArray[2],
                        ];
                        break;
                    case self::MOUNTAIN_REFERENCE:
                        $this->checkExactDataNumber($contentArray, 3);

                        $mountains[] = [
                            'x' => $contentArray[1],
                            'y' => $contentArray[2],
                        ];
                        break;
                    case self::TREASURE_REFERENCE:
                        $this->checkExactDataNumber($contentArray, 4);

                        $treasures[] = [
                            'x' => $contentArray[1],
                            'y' => $contentArray[2],
                            'counter' => $contentArray[3],
                        ];
                        break;
                    case self::ADVENTURER_REFERENCE:
                        $this->checkExactDataNumber($contentArray, 6);
                        $adventurers[] = [
                            'name' => $contentArray[1],
                            'x' => $contentArray[2],
                            'y'=> $contentArray[3],
                            'direction' => $contentArray[4],
                            'actions' => $contentArray[5],
                        ];
                        break;
                }
            }
        }

        return [
            self::MAP_REFERENCE => $mapDimension,
            self::MOUNTAIN_REFERENCE => $mountains,
            self::TREASURE_REFERENCE => $treasures,
            self::ADVENTURER_REFERENCE => $adventurers,
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
            case self::MAP_REFERENCE:
                $this->map = new Entities\Map($data['width'], $data['height'], $this->gameOutputPath);
                break;
            case self::MOUNTAIN_REFERENCE:
                $option = new Entities\MountainOption($data, $this->map);
                break;
            case self::TREASURE_REFERENCE:
                $option = new Entities\TreasureOption($data, $this->map);
                break;
            case self::ADVENTURER_REFERENCE:
                $option = new Entities\AdventurerOption($data, $this->map);
                $this->sortedAdventurers[] = $option;
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

    public function playGameConfiguration()
    {
//        var_dump($configurationContent);
    }
}
