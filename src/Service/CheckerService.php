<?php

namespace App\Service;

use App\Entities\AdventurerOption;

class CheckerService
{
    public static $allowedDirections = [
        AdventurerOption::NORTH_DIRECTION,
        AdventurerOption::SOUTH_DIRECTION,
        AdventurerOption::WEST_DIRECTION,
        AdventurerOption::EAST_DIRECTION,
    ];

    public static $allowedActions = [
        'A',
        'D',
        'G',
    ];

    public static function isIntegerAndMoreThanZero($allowZero, $varToTest, $max = null)
    {
        $varToTest = intval($varToTest);

        if ($varToTest < 0) {
            return false;
        }

        if (!$allowZero && $varToTest == 0) {
            return false;
        }

        if ($max !== null && $varToTest > $max) {
            return false;
        }

        return true;
    }

    public static function isValidAdventurerDirection($direction)
    {
        if (!in_array($direction, self::$allowedDirections)) {
            return false;
        }

        return true;
    }

    public static function isValidAdventurerActionSet(string $actionSet)
    {
        for ($i = 0; $i < strlen($actionSet); $i++) {
            $action = $actionSet[$i];

            if (!in_array($action, self::$allowedActions)) {
                return false;
            }
        }

        return true;
    }
}