<?php

require_once "Net/IPv6.php";

abstract class Net_Ipv6_Test_BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Net_IPv6
     */
    protected $ip;

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
