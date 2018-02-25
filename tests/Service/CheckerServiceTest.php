<?php

namespace App\Tests\Service;

use App\Tests\AbstractTestCase;
use App\Service\CheckerService;

class ExpectedErrorTest extends AbstractTestCase
{
    public function testIsIntegerAndMoreThanZero()
    {
        /** Errors */
        // zero not allowed
        $this->assertFalse(CheckerService::isIntegerAndMoreThanZero(false, 0));
        // zero not allowed even if max given
        $this->assertFalse(CheckerService::isIntegerAndMoreThanZero(false, 0, 5));
        // value > max
        $this->assertFalse(CheckerService::isIntegerAndMoreThanZero(true, 10, 5));


        /** Success */
        // zero allowed
        $this->assertTrue(CheckerService::isIntegerAndMoreThanZero(true, 0));
        // zero allowed with max
        $this->assertTrue(CheckerService::isIntegerAndMoreThanZero(true, 0, 10));
        // value < max
        $this->assertTrue(CheckerService::isIntegerAndMoreThanZero(true, 3, 5, true));
    }

    public function adventurerDirectionProvider()
    {
        return [
            ['N'],
            ['S'],
            ['E'],
            ['O'],
        ];
    }

    /**
     * @dataProvider adventurerDirectionProvider
     */
    public function testIsValidAdventurerDirectionTrue($direction)
    {
        $this->assertTrue(CheckerService::isValidAdventurerDirection($direction));
    }

    public function testIsValidAdventurerDirectionFalse()
    {
        $this->assertFalse(CheckerService::isValidAdventurerDirection('A'));
    }

    public function testIsValidAdventurerActionSet()
    {
        $this->assertTrue(CheckerService::isValidAdventurerActionSet('AGGDDADGAGDGADGAGDGA'));
        $this->assertFalse(CheckerService::isValidAdventurerActionSet('AFUYDGZFGFVYZGVF'));
    }
}