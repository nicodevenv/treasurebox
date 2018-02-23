<?php

namespace App\Service;

class CheckerService {
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
}