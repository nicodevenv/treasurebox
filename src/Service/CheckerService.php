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

    /**
     * @param bool     $allowZero
     * @param int      $varToTest
     * @param null|int $max
     *
     * @return bool
     */
    public static function isIntegerAndMoreThanZero(bool $allowZero, int $varToTest, int $max = null): bool
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

    /**
     * @param string $direction
     *
     * @return bool
     */
    public static function isValidAdventurerDirection(string $direction): bool
    {
        if (!in_array($direction, self::$allowedDirections)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $actionSet
     *
     * @return bool
     */
    public static function isValidAdventurerActionSet(string $actionSet): bool
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