<?php

/**
 * Created by PhpStorm.
 * User: nicolaschung
 * Date: 16/11/2017
 * Time: 10:28
 */
namespace App\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class AbstractWebTestCase
 * @package Tests\Weezevent\Ivar
 */
class AbstractTestCase extends TestCase
{
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    public function testNothing()
    {
        $this->assertTrue(true);
    }
}