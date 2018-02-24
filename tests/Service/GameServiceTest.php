<?php

namespace App\Tests\Service;

use App\Entities\AdventurerOption;
use App\Entities\Map;
use App\Entities\MountainOption;
use App\Entities\TreasureOption;
use App\Tests\Factory;
use App\Tests\AbstractTestCase;
use App\Service\GameService;

class GameServiceTest extends AbstractTestCase
{
    /** @var GameService */
    private $subject;

    public function setUp()
    {
        $this->subject = new GameService();

        parent::setUp();
    }

    public function testGenerateConfigurationFromArray()
    {
        if (file_exists(GameService::CONFIGURATION_PATH)) {
            unlink(GameService::CONFIGURATION_PATH);
        }

        $dataArray = [
            'This is a row test',
            'This is another row test',
        ];
        $this->subject->generateConfigurationFromArray($dataArray);
        $this->assertFileExists(GameService::CONFIGURATION_PATH);
    }

    public function testGetFile()
    {
        $dataArray = [
            'This is a row test',
            'This is another row test',
        ];
        $this->subject->generateConfigurationFromArray($dataArray);

        $this->assertEquals('resource' ,gettype($this->invokeMethod($this->subject, 'getFile', [GameService::CONFIGURATION_PATH])));
    }

    public function testCheckExactDataNumber()
    {
        $this->expectException(\Exception::class);
        $this->invokeMethod($this->subject, 'checkExactDataNumber', [[0, 1, 2, 3, 1, 2, 3, 4], 7]);
    }

    public function testSortGameConfigurationData()
    {
        new Factory\GameFactory();

        $result = $this->invokeMethod($this->subject, 'sortGameConfigurationData', []);

        $attempted = [
            'C' => [
                'width' =>'3',
                'height' => '4',
            ],
            'M' => [
                [
                    'x' => '1',
                    'y' => '0',
                ],
                [
                    'x' => '2',
                    'y' => '1',
                ]
            ],
            'T' => [
                [
                    'x' => '0',
                    'y' => '3',
                    'counter' => '2',
                ],
                [
                    'x' => '1',
                    'y' => '3',
                    'counter' => '3',
                ]
            ],
            'A' => [
                [
                    'name' => 'Lara',
                    'x' => '1',
                    'y' => '1',
                    'direction' => 'S',
                    'actions' => 'AADADAGGA',
                ]
            ]
        ];

        $this->assertEquals($attempted, $result);
    }

    public function prepareGameConfigurationProvider()
    {
        return [
            [1, 0, 0, MountainOption::class],
            [2, 1, 0, MountainOption::class],
            [0, 3, 0, TreasureOption::class],
            [1, 3, 0, TreasureOption::class],
            [1, 1, 0, AdventurerOption::class],
        ];
    }

    /**
     * @dataProvider prepareGameConfigurationProvider
     * @throws \Exception
     */
    public function testPrepareGameConfiguration($x, $y, $index, $attemptedClass)
    {
        new Factory\GameFactory();

        $this->subject->prepareGameConfiguration();
        $mapFrames = $this->subject->getMap()->getMapFrames();

        $this->assertInstanceOf($attemptedClass, $mapFrames[$y][$x][$index]);
    }

    public function createEntitiesProvider()
    {
        return [
            ['C', ['width' => '5', 'height' => '5'], Map::class],
            ['M', ['x' => '1', 'y' => '1'], MountainOption::class, ['width' => '5', 'height' => '5']],
            ['T', ['x' => '1', 'y' => '1', 'counter' => '2'], TreasureOption::class, ['width' => '5', 'height' => '5']],
            ['A', [
                'name' => 'Lara',
                'x' => '1',
                'y' => '1',
                'direction' => 'N',
                'actions' => 'AADA',
            ], AdventurerOption::class, ['width' => '5', 'height' => '5']],
        ];
    }

    /**
     * @dataProvider createEntitiesProvider
     */
    public function testCreateEntities($type, $data, $attemptedClass, $mapData = null)
    {
        if ($attemptedClass === Map::class) {
            $this->invokeMethod($this->subject, 'createEntities', [$type, $data]);
            $map = $this->subject->getMap();
            $this->assertEquals($map->getWidth(), $data ['width']);
            $this->assertEquals($map->getHeight(), $data ['height']);
            return;
        }

        $this->invokeMethod($this->subject, 'createEntities', ['C', $mapData]);

        $this->invokeMethod($this->subject, 'createEntities', [$type, $data]);
        $mapFrames = $this->subject->getMap()->getMapFrames();
        $this->assertInstanceOf($attemptedClass, $mapFrames[$data['y']][$data['x']][0]);
    }
}