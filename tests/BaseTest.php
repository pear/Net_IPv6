<?php

require_once "Net/IPv6.php";
require_once "PHPUnit/Framework/TestCase.php";

abstract class BaseTest extends PHPUnit_Framework_TestCase
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
