<?php

namespace App\Service;

class CheckerService {
    public static $allowedDirections = [
        'N',
        'S',
        'E',
        'W',
    ];

    public static $allowedActions = [
        'A',
        'D',
        'G',
    ];

    public static function isIntegerAndMoreThanZero($allowZero, $varToTest, $max = null)
    {
        if (ctype_digit($varToTest)) {
            if ($varToTest < 0) {
                return false;
            }

            if (!$allowZero && $varToTest > 0) {
                return true;
            }

            if ($max !== null && $varToTest > $max) {
                return false;
            }

            return true;
        }

        return false;
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
        for ($i=0; $i < strlen($actionSet); $i++) {
            $action = $actionSet[$i];

            if (!in_array($action, self::$allowedActions)) {
                return false;
            }
        }

        return true;
    }
}