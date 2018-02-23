<?php
namespace App\Service;

use App\Entities;
use App\Service\CheckerService;


class GameService {
    private $gameConfigurationPath = 'public/inputs/game_configuration.txt';

    private $gameOutputPath = 'public/output/game_output.txt';

    private $sortedAdventurers = [];

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
                    case 'C':
                        if (!empty($mapDimension)) {
                            throw new \Exception('Map dimensions are defined multiple times');
                        }

                        $this->checkExactDataNumber($contentArray, 3);
                        $mapDimension = [
                            'width' => $contentArray[1],
                            'height' => $contentArray[2],
                        ];
                        break;
                    case 'M':
                        $this->checkExactDataNumber($contentArray, 3);

                        $mountains[] = [
                            'x' => $contentArray[1],
                            'y' => $contentArray[2],
                        ];
                        break;
                    case 'T':
                        $this->checkExactDataNumber($contentArray, 4);

                        $treasures[] = [
                            'x' => $contentArray[1],
                            'y' => $contentArray[2],
                            'counter' => $contentArray[3],
                        ];
                        break;
                    case 'A':
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
            'C' => $mapDimension,
            'M' => $mountains,
            'T' => $treasures,
            'A' => $adventurers,
        ];
    }

    /**
     * @throws \Exception
     */
    public function prepareGameConfiguration()
    {
        $sortedData = $this->sortGameConfigurationData();

        foreach ($sortedData as $type => $data) {
            $this->createEntities($type, $data);
        }

        var_dump($sortedData);
        exit();
    }

    /**
     * @param $type
     * @param $data
     *
     * @throws \Exception
     */
    private function createEntities($type, $data)
    {
        switch($type) {
            case 'C':
                $this->map = new Entities\Map($data['width'], $data['height'], $this->gameOutputPath);
                break;
            case 'M':
                $mountain = new Entities\MountainOption($data, $this->map);
        }
    }

    public function playGameConfiguration()
    {
//        var_dump($configurationContent);
    }
//    private $map;
//
//    private $adventurers;
//
//    private $mountains;
//
//    private $treasures;
//
//    private $disabledPositions = [];
//
//    private $errors = [];
//
//    private function isValidXY($separator, $data, $type, $index = '', $maxXY = [])
//    {
//        $errorMessage = '';
//        $xy = explode($separator, $data);
//
//        if (count($maxXY) === 0
//            && (
//                !$this->isIntegerAndMoreThanZero($xy[0])
//                || !$this->isIntegerAndMoreThanZero($xy[1])
//            )
//        ) {
//            $errorMessage = $type . $index . ' x / y must be integers and more than 0 !';
//        }
//
//        if (count($maxXY) === 2
//            && (
//                !$this->isIntegerAndMoreThanZero($xy[0], $maxXY[0])
//                || !$this->isIntegerAndMoreThanZero($xy[1], $maxXY[1])
//            )
//        ) {
//            $errorMessage = $type . $index;
//            $errorMessage .= ' x / y must be integers, more than 0 and';
//            $errorMessage .= ' less than ' . $maxXY[0] . $separator . $maxXY[1] . ' !';
//        }
//
//        if (!in_array(count($maxXY), [0, 2])) {
//            echo 'case not implemented';
//            exit();
//        }
//
//        if ($errorMessage != '') {
//            $this->errors[] = $errorMessage;
//            return false;
//        }
//
//        return true;
//    }
//
//    private function addDisabledPositions($position)
//    {
//        if (!in_array($position, $this->disabledPositions)) {
//            $this->errors[] = 'The current position is already locked by another option : ' . $position;
//            return;
//        }
//        $this->errors[] = $position;
//    }

//    public function generateConfigurationFromArray($data)
//    {
//        if ($this->isValidXY('*', $data['map_dimension'], 'Map dimensions')) {
//            $mapDimensions = explode('*', $data['map_dimension']);
//            foreach ($mapDimensions as $index => $mapDimension) {
//                $mapDimensions[$index]--;
//            }
//
//            foreach ($data['treasures'] as $index => $treasure) {
//                $this->isValidXY(',', $treasure, 'Treasure', $index, $mapDimensions);
//            }
//
//            foreach ($data['mountains'] as $index => $mountain) {
//                if ($this->isValidXY(',', $mountain, 'Mountain', $index, $mapDimensions)) {
//                    $this->addDisabledPositions($mountain);
//                }
//            }
//
//            foreach ($data['adventurers'] as $adventurer) {
//                $this->isValidXY(',', $adventurer['position'], $adventurer['name'], '', $mapDimensions);
//            }
//        }
//
//        var_dump($this->errors);
//        exit();
//    }
}
