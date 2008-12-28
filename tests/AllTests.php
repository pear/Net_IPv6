<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Net_IPv6_AllTests::main');
}

// PHPUnit inlcudes
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';


require_once 'NetIPv6Test.php';

class Net_IPv6_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Net_IPv6 Tests');
        $suite->addTestSuite('NetIPv6Test');
        return $suite;
    }
}


// exec test suite
if (PHPUnit_MAIN_METHOD == 'Net_IPv6_AllTests::main') {
    Net_IPv6_AllTests::main();
}
?>
