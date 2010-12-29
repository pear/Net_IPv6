<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Alexander Merz <alexander.merz@web.de>				  |
// +----------------------------------------------------------------------+
//
// $Id$

require_once "Net/IPv6.php";
require_once "PHPUnit/Framework/TestCase.php";

/**
* This testcases tests for several bugs and general topics
*
* @author  Alexander Merz <alexander.merz@t-online.de>
* @package Net_IPv6
* @version $Id$
* @access  public
*/
class NetIPv6Test extends PHPUnit_Framework_TestCase {

    protected $ip;

    public function setUp() {
        $this->ip = new Net_IPv6();
    }

    /**
     * tests if checkIPv6 can handle prefix length
     */
    public function testCheckIPv6WithPrefix() {
        $testip = "FE80:FFFF:0:FFFF:129:144:52:38/60";
        $is     = $this->ip->checkIPv6($testip);

        $this->assertTrue($is);
    }

    /**
     * tests isInNetmask() with no netmask length given
     */
    public function testIsInNetmaskNoNetmask() {
        $testip = "FE80:FFFF:0:FFFF:129:144:52:38";
        $testprefix = "EE80::";
        $is = $this->ip->isInNetmask($testip, $testprefix);
        $this->assertTrue(PEAR::isError($is));
    }

    /**
     * tests isInNetmask() with the netmask length in
     * the third parameter
     */
    public function testIsInNetmaskWithBitsAsParameter() {
        $testip = "FE80:FFFF:0:FFFF:129:144:52:38";
        $testprefix = "FE80::";
        $is = $this->ip->isInNetmask($testip, $testprefix, 16);
        $this->assertTrue($is);
    }

    /**
     * tests isInNetmask() with the netmask length in
     * the second parameter
     */
    public function testIsInNetmaskWithBitsInNetmask() {
        $testip = "FE80:FFFF:0:FFFF:129:144:52:38";
        $testprefix = "FE80::/16";
        $is = $this->ip->isInNetmask($testip, $testprefix);
        $this->assertTrue($is);
    }

    /**
     * tests isInNetmask() with the netmask length in
     * the first parameter
     */
    public function testIsInNetmaskWithBitsInIP() {
        $testip = "FE80:FFFF:0:FFFF:129:144:52:38/16";
        $testprefix = "FE80::";
        $is = $this->ip->isInNetmask($testip, $testprefix);
        $this->assertTrue($is);
    }

    /**
     * tests getNetmask with two parameters
     */
    public function testGetNetmaskTwoParameters() {
        $testip = "FE80:0:0:FFFF:129:144:52:38";
        $is = $this->ip->getNetmask($testip, 16);
        $this->assertEquals( "fe80:0:0:0:0:0:0:0", $is);
    }

    /**
     * tests getNetmask with one parameter
     */
    public function testGetNetmaskOneParameter() {
        $testip = "FE80:0:0:FFFF:129:144:52:38/16";
        $is = $this->ip->getNetmask($testip);
        $this->assertEquals( "fe80:0:0:0:0:0:0:0", $is);
    }

    /**
     * test getAddressType - Link Local
     */
    public function testGetAddressTypeLinkLocal() {
        $testip = "FE80:0:0:FFFF:129:144:52:38";
        $is = $this->ip->getAddressType($testip);
        $this->assertEquals( NET_IPV6_LOCAL_LINK, $is);
    }

    /**
     * test getAddressType - Unassigned
     */
    public function testGetAddressTypeUnassigned() {
        $testip = "E000:0:0:FFFF:129:144:52:38";
        $is = $this->ip->getAddressType($testip);
        $this->assertEquals( NET_IPV6_UNASSIGNED, $is);
    }

    /**
     * test the Bin2Ip method
     */
    public function testBin2Ip() {
        $testip = "1111111111111111".
                  "0000000000000000".
                  "0000000000000000".
                  "1111111111111111".
                  "0000000100101001".
                  "0000000101000100".
                  "0000000001010010".
                  "0000000000111000";
        $is = $this->ip->_bin2Ip($testip);
        $this->assertEquals( "ffff:0:0:ffff:129:144:52:38", $is);
    }


    /**
     * test the IP2Bin method with an uncompressed ip
     */
    public function testIp2BinUncompressed() {
        $testip = "ffff:0:0:FFFF:129:144:52:38";
        $is = $this->ip->_ip2Bin($testip);
        $this->assertEquals( "1111111111111111".
                             "0000000000000000".
                             "0000000000000000".
                             "1111111111111111".
                             "0000000100101001".
                             "0000000101000100".
                             "0000000001010010".
                             "0000000000111000"
                             ,$is);
    }


    /**
     * test the IP2Bin method with a compressed ip
     */
    public function testIp2BinCompressed() {
        $testip = "ffff::FFFF:129:144:52:38";
        $is = $this->ip->_ip2Bin($testip);
        $this->assertEquals( "1111111111111111".
                             "0000000000000000".
                             "0000000000000000".
                             "1111111111111111".
                             "0000000100101001".
                             "0000000101000100".
                             "0000000001010010".
                             "0000000000111000"
                             ,$is);
    }


    /**
    * this testcase handles Bug 4977
    * which covers the problem with wrong compressing where nothing is to
    * compress and zeros are replaced by ':'
    *
    */
    public function testBug4977() {
        $testip = "2001:ec8:1:1:1:1:1:1";
        $is = $this->ip->compress($testip);
        $this->assertEquals( "2001:ec8:1:1:1:1:1:1", $is);
    }

    /**
    * this testcase handles Bug 3851
    * which covers the problem with uncompressing with an IPv4 part
    * in the ip
    *
    */
    public function testBug3851() {
        $testip = "ffff::FFFF:129.144.52.38";
        $is = $this->ip->uncompress($testip);
        $this->assertEquals( "ffff:0:0:0:0:FFFF:129.144.52.38", $is);
    }

    /**
    * this testcase handles Bug 3405
    * which covers the problem with compressing 0000
    * in the ip
    *
    */
    public function testBug3405() {
        $testip = "2010:0588:0000:faef:1428:0000:0000:57ab";
        $is = $this->ip->compress($testip);
        $this->assertEquals( "2010:588:0:faef:1428::57ab", $is);
    }

    /**
     * this testcase handles Bug 14747
     * which covers already compressed adresses
     * to keep as is
     */
    public function testBug14747_CompressShouldDoNothingOnCompressedIPs() {

        $testip = '2001:503:ba3e::2:30';
        $is = $this->ip->compress($testip);

        $this->assertEquals("2001:503:ba3e::2:30", $is);

        $testip = 'ff01::101';
        $is = $this->ip->compress($testip);

        $this->assertEquals("ff01::101", $is);
    }


    /**
    * this testcase handles Bug 2802
    * which covers the problem with compressing 0000
    * in the ip
    *
    */
    public function testBug2802() {
        $testip = "0000:0000:0000:588:0000:FAEF:1428:57AB";
        $is = $this->ip->compress($testip);
        $this->assertEquals( "::588:0:faef:1428:57ab", $is);
    }

    /**
    * this testcase handles Bug 2803
    * which covers the problem adding a unnec. colon at the end
    * in the ip
    *
    */
    public function testBug2803() {
        $testip = "0:0:0:0588:0:FAEF:1428:57AB";
        $is = $this->ip->compress($testip);
        $this->assertEquals( "::588:0:faef:1428:57ab", $is);
    }

    /**
    * handle Bug 12442
    * Netmask is miss assigned during compression/uncompression
    */
    public function testBug12442() {

        $testip = "2001:4abc:abcd:0000:3744:0000:0000:0000/120";
        $is = $this->ip->compress($testip);
        $this->assertEquals( "2001:4abc:abcd:0:3744::/120", $is);

        $testip = "2001:4abc:abcd:0:3744::/120";
        $is = $this->ip->uncompress($testip);
        $this->assertEquals( "2001:4abc:abcd:0:3744:0:0:0/120", $is);
    }

    /**
    * handle Bug 15947
    * checkIpv6 returns true although IP is too long
    */
    public function testBug15947_IpTooLong() {

        $testIp = '2001:0ec8:0000:0000:0000:0000:0000:0001111';

        $is = $this->ip->checkIPv6($testIp);

        $this->assertFalse($is);

    }

    /**
    * this testcase handles compress
    *
    */
    public function testCompress1() {
        $testip = "FF01:0:0:0:0:0:0:101";
        $is = $this->ip->compress($testip);
        $this->assertEquals( "ff01::101", $is);
    }

    /**
    * this testcase handles compress
    *
    */
    public function testCompress2() {
        $testip = "0:0:0:0:0:0:0:1";
        $is = $this->ip->compress($testip);
        $this->assertEquals( "::1", $is);
    }

    /**
    * this testcase handles compress
    *
    */
    public function testCompress3() {
        $testip = "1:0:0:0:0:0:0:0";
        $is = $this->ip->compress($testip);
        $this->assertEquals( "1::", $is);
    }

    /**
    * this testcase handles forced compression
    *
    */
    public function testCompressForced() {
        $testip = "FF01::0:1";
        $is = $this->ip->compress($testip, true);
        $this->assertEquals( "FF01::1", $is);
    }

    /**
    * this testcase handles compress with a prefix length spec
    *
    */
    public function testCompressWithPrefixLength() {
        $testip = "0000:0000:0000:0000:0000:ffff:5056:5000/116";
        $is = $this->ip->compress($testip);
        $this->assertEquals( "::ffff:5056:5000/116", $is);
    }


    /**
    * this testcase handles uncompress
    *
    */
    public function testUncompress1() {
        $testip = "ff01::101";
        $is = $this->ip->uncompress($testip);
        $this->assertEquals( "ff01:0:0:0:0:0:0:101", $is);
    }

    /**
    * this testcase handles uncompress
    *
    */
    public function testUncompress2() {
        $testip = "::1";
        $is = $this->ip->uncompress($testip);
        $this->assertEquals( "0:0:0:0:0:0:0:1", $is);
    }

    /**
    * this testcase handles uncompress
    *
    */
    public function testUncompress3() {
        $testip = "1::";
        $is = $this->ip->uncompress($testip);
        $this->assertEquals( "1:0:0:0:0:0:0:0", $is);
    }

    /**
    * this testcase handles uncompress with a prefix length spec
    *
    */
    public function testUncompressWithPrefixLength() {
        $testip = "::ffff:5056:5000/116";
        $is     = $this->ip->uncompress($testip);

        $this->assertEquals( "0:0:0:0:0:ffff:5056:5000/116", $is);
    }

    /**
    * this testcase handles uncompress adding leading zeros
    *
    */
    public function testUncompress1WithLeadingZeros() {
        $testip = "ff01::101";
        $is = $this->ip->uncompress($testip, true);
        $this->assertEquals( "ff01:0000:0000:0000:0000:0000:0000:0101", $is);
    }

    /**
    * this testcase handles uncompress adding leading zeros
    *
    */
    public function testUncompress2WithLeadingZeros() {
        $testip = "::1";
        $is = $this->ip->uncompress($testip, true);
        $this->assertEquals( "0000:0000:0000:0000:0000:0000:0000:0001", $is);
    }

    /**
    * this testcase handles uncompress adding leading zeros
    *
    */
    public function testUncompress3WithLeadingZeros() {
        $testip = "1::";
        $is = $this->ip->uncompress($testip, true);
        $this->assertEquals( "0001:0000:0000:0000:0000:0000:0000:0000", $is);
    }

    /**
    * this testcase handles uncompress with a prefix length spec adding leading zeros
    *
    */
    public function testUncompressWithPrefixLengthWithLeadingZeros() {
        $testip = "::ffff:5056:5000/116";
        $is     = $this->ip->uncompress($testip, true);

        $this->assertEquals( "0000:0000:0000:0000:0000:ffff:5056:5000/116", $is);
    }

    /**
    * this testcase handles get Prefix length
    *
    */
    public function testGetPrefixLength() {
        $testip = "0000:0000:0000:0000:0000:ffff:5056:5000/116";
        $prefix = $this->ip->getPrefixLength($testip);

        $this->assertEquals( "116", $prefix);
    }

    /**
    * this testcase handles remove a Prefix length
    *
    */
    public function testRemovePrefixLength() {
        $testip = "0000:0000:0000:0000:0000:ffff:5056:5000/116";

        $ip = $this->ip->removePrefixLength($testip);

        $this->assertEquals( "0000:0000:0000:0000:0000:ffff:5056:5000", $ip);
    }

    public function testParseAddress() {

        $testip = "2001:502:f3ff::/48";

        $result = $this->ip->parseAddress($testip);

        $this->assertEquals( "2001:502:f3ff:0:0:0:0:0", $result['start']);
        $this->assertEquals( "2001:502:f3ff:ffff:ffff:ffff:ffff:ffff", $result['end']);

    }

}
