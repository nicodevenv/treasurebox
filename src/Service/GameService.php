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

    private function isValidXY($separator, $data, $type, $index = '', $maxXY = [])
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
            $errorMessage = $type . $index;
            $errorMessage .= ' x / y must be integers, more than 0 and';
            $errorMessage .= ' less than ' . $maxXY[0] . $separator . $maxXY[1] . ' !';
        }

        if (!in_array(count($maxXY), [0, 2])) {
            echo 'case not implemented';
            exit();
        }

        if ($errorMessage != '') {
            $this->errors[] = $errorMessage;
            return false;
        }

        return true;
    }

    private function addDisabledPositions($position)
    {
        if (!in_array($position, $this->disabledPositions)) {
            $this->errors[] = 'The current position is already locked by another option : ' . $position;
            return;
        }
        $this->errors[] = $position;
    }

    public function generateConfigurationFromArray($data)
    {
        if ($this->isValidXY('*', $data['map_dimension'], 'Map dimensions')) {
            $mapDimensions = explode('*', $data['map_dimension']);
            foreach ($mapDimensions as $index => $mapDimension) {
                $mapDimensions[$index]--;
            }

            foreach ($data['treasures'] as $index => $treasure) {
                $this->isValidXY(',', $treasure, 'Treasure', $index, $mapDimensions);
            }

            foreach ($data['mountains'] as $index => $mountain) {
                if ($this->isValidXY(',', $mountain, 'Mountain', $index, $mapDimensions)) {
                    $this->addDisabledPositions($mountain);
                }
            }

            foreach ($data['adventurers'] as $adventurer) {
                $this->isValidXY(',', $adventurer['position'], $adventurer['name'], '', $mapDimensions);
            }
        }

        var_dump($this->errors);
        exit();
    }
}