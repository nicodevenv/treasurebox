<?php
namespace App\Service;


class GameService {
    private $map;

    private $adventurers;

    private $mountains;

    private $treasures;

    private $disabledPositions;

    private $errors = [];

    private function isIntegerAndMoreThanZero($varToTest, $max = null)
    {
        if (ctype_digit($varToTest) && $varToTest > 0) {
            if ($max !== null && $varToTest > $max) {
                return false;
            }
            return true;
        }
        return false;
    }

    private function checkXY($separator, $data, $type, $index = '', $maxXY = [])
    {
        $errorMessage = '';
        $xy = explode($separator, $data);

        if (count($maxXY) === 0
            && (
                !$this->isIntegerAndMoreThanZero($xy[0])
                || !$this->isIntegerAndMoreThanZero($xy[1])
            )
        ) {
            $errorMessage = $type . $index . ' x / y must be integers and more than 0 !';
        }

        if (count($maxXY) === 2
            && (
                !$this->isIntegerAndMoreThanZero($xy[0], $maxXY[0])
                || !$this->isIntegerAndMoreThanZero($xy[1], $maxXY[1])
            )
        ) {
            $errorMessage = $type . $index . ' x / y must be integers, more than 0 and less than ' . $xy[0] . $separator . $xy[1] . ' !';
        }

        if (!in_array(count($maxXY), [0, 2])) {
            echo 'case not implemented';
            exit();
        }

        if ($errorMessage != '') {
            $this->errors[] = $errorMessage;
        }
    }

    public function generateConfigurationFromArray($data)
    {
        $this->checkXY('*', $data['map_dimension'], 'Map dimensions');

        $mapDimensions = explode('*', $data['map_dimension']);
        foreach ($mapDimensions as $index => $mapDimension) {
            $mapDimensions[$index]--;
        }

        foreach ($data['treasures'] as $index => $treasure) {
            $this->checkXY(',', $treasure, 'Treasure', $index, $mapDimensions);
        }

        foreach ($data['mountains'] as $index => $mountain) {
            $this->checkXY(',', $mountain, 'Mountain', $index, $mapDimensions);
        }

        foreach ($data['adventurers'] as $adventurer) {
            $this->checkXY(',', $adventurer['position'], $adventurer['name'], '', $mapDimensions);
        }

        var_dump($this->errors);
        exit();
    }
}