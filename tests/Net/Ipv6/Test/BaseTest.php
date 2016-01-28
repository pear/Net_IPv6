<?php

require_once "Net/IPv6.php";

abstract class Net_Ipv6_Test_BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Net_IPv6
     */
    protected $ip;

    /**
     * Call to protected functions
     *
     * @return object
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Net_IPv6');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * Setup object.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->ip = new Net_IPv6();
    }
}
