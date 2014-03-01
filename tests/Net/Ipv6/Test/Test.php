<?php
/**
 * +----------------------------------------------------------------------+
 * | PHP Version 4                                                        |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 1997-2003 The PHP Group                                |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 2.0 of the PHP license,       |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available at through the world-wide-web at                           |
 * | http://www.php.net/license/2_02.txt.                                 |
 * | If you did not receive a copy of the PHP license and are unable to   |
 * | obtain it through the world-wide-web, please send a note to          |
 * | license@php.net so we can mail you a copy immediately.               |
 * +----------------------------------------------------------------------+
 * | Authors: Alexander Merz <alexander.merz@web.de>                      |
 * +----------------------------------------------------------------------+
 *
 * PHP Version 5
 */

require_once 'BaseTest.php';

/**
 * This testcases for general topics
 *
 * @package Net_IPv6
 * @author  Alexander Merz <alexander.merz@t-online.de>
 * @version Release: @package_version@
 */
class Net_IPv6_Test_Test extends Net_Ipv6_Test_BaseTest
{
    /**
     * tests if checkIPv6 can handle prefix length
     *
     * @return void
     */
    public function testCheckIPv6WithPrefix()
    {
        $testip = "FE80:FFFF:0:FFFF:129:144:52:38/60";
        $is     = $this->ip->checkIPv6($testip);

        $this->assertTrue($is);
    }

    /**
     * tests isInNetmask() with no netmask length given
     *
     * @return void
     */
    public function testIsInNetmaskNoNetmask()
    {
        $testip = "FE80:FFFF:0:FFFF:129:144:52:38";
        $testprefix = "EE80::";
        $is = $this->ip->isInNetmask($testip, $testprefix);
        $this->assertTrue(PEAR::isError($is));
    }

    /**
     * tests isInNetmask() with the netmask length in
     * the third parameter
     *
     * @return void
     */
    public function testIsInNetmaskWithBitsAsParameter()
    {
        $testip = "FE80:FFFF:0:FFFF:129:144:52:38";
        $testprefix = "FE80::";
        $is = $this->ip->isInNetmask($testip, $testprefix, 16);
        $this->assertTrue($is);
    }

    /**
     * tests isInNetmask() with the netmask length in
     * the second parameter
     *
     * @return void
     */
    public function testIsInNetmaskWithBitsInNetmask()
    {
        $testip = "FE80:FFFF:0:FFFF:129:144:52:38";
        $testprefix = "FE80::/16";
        $is = $this->ip->isInNetmask($testip, $testprefix);
        $this->assertTrue($is);
    }

    /**
     * tests isInNetmask() with the netmask length in
     * the first parameter
     *
     * @return void
     */
    public function testIsInNetmaskWithBitsInIP()
    {
        $testip = "FE80:FFFF:0:FFFF:129:144:52:38/16";
        $testprefix = "FE80::";
        $is = $this->ip->isInNetmask($testip, $testprefix);
        $this->assertTrue($is);
    }

    /**
     * tests getNetmask with two parameters
     *
     * @return void
     */
    public function testGetNetmaskTwoParameters()
    {
        $testip = "FE80:0:0:FFFF:129:144:52:38";
        $is = $this->ip->getNetmask($testip, 16);
        $this->assertEquals("fe80:0:0:0:0:0:0:0", $is);
    }

    /**
     * tests getNetmask with one parameter
     *
     * @return void
     */
    public function testGetNetmaskOneParameter()
    {
        $testip = "FE80:0:0:FFFF:129:144:52:38/16";
        $is = $this->ip->getNetmask($testip);
        $this->assertEquals("fe80:0:0:0:0:0:0:0", $is);
    }

    /**
     * test getAddressType - Link Local
     *
     * @return void
     */
    public function testGetAddressTypeLinkLocal()
    {
        $testip = "FE80:0:0:FFFF:129:144:52:38";
        $is = $this->ip->getAddressType($testip);
        $this->assertEquals(NET_IPV6_LOCAL_LINK, $is);
    }

    /**
     * test getAddressType - Unassigned
     *
     * @return void
     */
    public function testGetAddressTypeUnassigned()
    {
        $testip = "E000:0:0:FFFF:129:144:52:38";
        $is = $this->ip->getAddressType($testip);
        $this->assertEquals(NET_IPV6_UNASSIGNED, $is);
    }

    /**
     * test the Bin2Ip method
     *
     * @return void
     */
    public function testBin2Ip()
    {
        $testip = "1111111111111111".
                  "0000000000000000".
                  "0000000000000000".
                  "1111111111111111".
                  "0000000100101001".
                  "0000000101000100".
                  "0000000001010010".
                  "0000000000111000";
        $is = $this->ip->_bin2Ip($testip);
        $this->assertEquals("ffff:0:0:ffff:129:144:52:38", $is);
    }


    /**
     * test the IP2Bin method with an uncompressed ip
     *
     * @return void
     */
    public function testIp2BinUncompressed()
    {
        $testip = "ffff:0:0:FFFF:129:144:52:38";
        $is = $this->ip->_ip2Bin($testip);
        $this->assertEquals(
            "1111111111111111".
            "0000000000000000".
            "0000000000000000".
            "1111111111111111".
            "0000000100101001".
            "0000000101000100".
            "0000000001010010".
            "0000000000111000",
            $is
        );
    }


    /**
     * test the IP2Bin method with a compressed ip
     *
     * @return void
     */
    public function testIp2BinCompressed()
    {
        $testip = "ffff::FFFF:129:144:52:38";
        $is = $this->ip->_ip2Bin($testip);
        $this->assertEquals(
            "1111111111111111".
            "0000000000000000".
            "0000000000000000".
            "1111111111111111".
            "0000000100101001".
            "0000000101000100".
            "0000000001010010".
            "0000000000111000",
            $is
        );
    }

    /**
     * Provider for {@link self::testCompress}.
     *
     * @return array
     */
    public static function compressProvider()
    {
        return array(
            array("FF01:0:0:0:0:0:0:101", "ff01::101", false),
            array("0:0:0:0:0:0:0:1", "::1", false),
            array("1:0:0:0:0:0:0:0", "1::", false),
            array("FF01::0:1", "ff01::1", true),
            // with prefix length spec
            array(
                "0000:0000:0000:0000:0000:ffff:5056:5000/116",
                "::ffff:5056:5000/116",
                false
            ),
        );
    }

    /**
     * this testcase handles compress
     *
     * @param string  $testip      The IP to compress.
     * @param string  $expectation The expected value.
     * @param boolean $force       Force compression.
     *
     * @return void
     * @dataProvider compressProvider
     */
    public function testCompress($testip, $expectation, $force)
    {
        $is = $this->ip->compress($testip, $force);
        $this->assertEquals($expectation, $is);
    }

    /**
     * Provider for {@link self::testUncompress()}.
     *
     * @return array
     */
    public static function uncompressProvider()
    {
        return array(
            array("ff01::101", "ff01:0:0:0:0:0:0:101", false),
            array("::1", "0:0:0:0:0:0:0:1", false),
            array("1::", "1:0:0:0:0:0:0:0", false),
            // with prefix length spec
            array("::ffff:5056:5000/116", "0:0:0:0:0:ffff:5056:5000/116", false),

            // leading zeros
            array("ff01::101", "ff01:0000:0000:0000:0000:0000:0000:0101", true),
            array("::1", "0000:0000:0000:0000:0000:0000:0000:0001", true),
            array("1::", "0001:0000:0000:0000:0000:0000:0000:0000", true),
            array(
                "::ffff:5056:5000/116",
                "0000:0000:0000:0000:0000:ffff:5056:5000/116",
                true
            )
        );
    }

    /**
     * this testcase handles uncompress
     *
     * @param string  $testip       The IP to uncompress.
     * @param string  $expectation  The expected value.
     * @param boolean $leadingZeros To use leading zeros or not.
     *
     * @return void
     * @dataProvider uncompressProvider
     */
    public function testUncompress($testip, $expectation, $leadingZeros)
    {
        $is = $this->ip->uncompress($testip, $leadingZeros);
        $this->assertEquals($expectation, $is);
    }

    /**
     * this testcase handles get Prefix length
     *
     * @return void
     */
    public function testGetPrefixLength()
    {
        $testip = "0000:0000:0000:0000:0000:ffff:5056:5000/116";
        $prefix = $this->ip->getPrefixLength($testip);

        $this->assertEquals("116", $prefix);
    }

    /**
     * this testcase handles remove a Prefix length
     *
     * @return void
     */
    public function testRemovePrefixLength()
    {
        $testip = "0000:0000:0000:0000:0000:ffff:5056:5000/116";

        $ip = $this->ip->removePrefixLength($testip);

        $this->assertEquals("0000:0000:0000:0000:0000:ffff:5056:5000", $ip);
    }

    /**
     * Parse address.
     *
     * @return void
     */
    public function testParseAddress()
    {
        $testip = "2001:502:f3ff::/48";

        $result = $this->ip->parseAddress($testip);

        $this->assertEquals("2001:502:f3ff:0:0:0:0:0", $result['start']);
        $this->assertEquals(
            "2001:502:f3ff:ffff:ffff:ffff:ffff:ffff",
            $result['end']
        );

    }

}
