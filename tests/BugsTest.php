<?php
/**
 * Test case for bugs reported/fixed.
 *
 * PHP Version 5
 *
 * @category Testing
 * @package  Net_IPv6
 * @author   Till Klampaeckel <till@php.net>
 * @license  BSD-2-Clause
 * @version  GIT: <git_id>
 * @link     http://pear.php.net/package/Net_IPv6
 */

require_once 'BaseTest.php';

/**
 * This testcases tests for several bugs and general topics
 *
 * @category Testing
 * @package  Net_IPv6
 * @author   Alexander Merz <alexander.merz@t-online.de>
 * @author   Phil Davis
 * @license  BSD-2-Clause
 * @version  Release: @package_version@
 */
class BugsTest extends BaseTest
{
    /**
     * this testcase handles Bug 19334
     * CheckIpv6 returned true because of an invalid check
     * non-valid chars
     *
     * @return void
     */
    public function testBug19334()
    {
        $testip = "fe80::16da:e9ff:fe0f:6dd4/64:48866";
        $this->assertFalse($this->ip->checkIPv6($testip));
    }

    /**
     * this testcase handles Bug 4977
     * which covers the problem with wrong compressing where nothing is to
     * compress and zeros are replaced by ':'
     *
     * @return void
     */
    public function testBug4977()
    {
        $testip = "2001:ec8:1:1:1:1:1:1";
        $is = $this->ip->compress($testip);
        $this->assertEquals("2001:ec8:1:1:1:1:1:1", $is);
    }

    /**
     * this testcase handles Bug 3851
     * which covers the problem with uncompressing with an IPv4 part
     * in the ip
     *
     * @return void
     */
    public function testBug3851()
    {
        $testip = "ffff::FFFF:129.144.52.38";
        $is = $this->ip->uncompress($testip);
        $this->assertEquals("ffff:0:0:0:0:FFFF:129.144.52.38", $is);
    }

    /**
     * this testcase handles Bug 3405
     * which covers the problem with compressing 0000
     * in the ip
     *
     * @return void
     */
    public function testBug3405()
    {
        $testip = "2010:0588:0000:faef:1428:0000:0000:57ab";
        $is = $this->ip->compress($testip);
        $this->assertEquals("2010:588:0:faef:1428::57ab", $is);
    }

    /**
     * this testcase handles Bug 14747
     * which covers already compressed adresses
     * to keep as is
     *
     * @return void
     */
    public function testBug14747_CompressShouldDoNothingOnCompressedIPs()
    {
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
     * @return void
     */
    public function testBug2802()
    {
        $testip = "0000:0000:0000:588:0000:FAEF:1428:57AB";
        $is = $this->ip->compress($testip);
        $this->assertEquals("::588:0:faef:1428:57ab", $is);
    }

    /**
     * this testcase handles Bug 2803
     * which covers the problem adding a unnec. colon at the end
     * in the ip
     *
     * @return void
     */
    public function testBug2803()
    {
        $testip = "0:0:0:0588:0:FAEF:1428:57AB";
        $is = $this->ip->compress($testip);
        $this->assertEquals("::588:0:faef:1428:57ab", $is);
    }

    /**
     * handle Bug 12442
     * Netmask is miss assigned during compression/uncompression
     *
     * @return void
     */
    public function testBug12442()
    {
        $testip = "2001:4abc:abcd:0000:3744:0000:0000:0000/120";
        $is = $this->ip->compress($testip);
        $this->assertEquals("2001:4abc:abcd:0:3744::/120", $is);

        $testip = "2001:4abc:abcd:0:3744::/120";
        $is = $this->ip->uncompress($testip);
        $this->assertEquals("2001:4abc:abcd:0:3744:0:0:0/120", $is);
    }

    /**
     * handle Bug 15947
     * checkIpv6 returns true although IP is too long
     *
     * @return void
     */
    public function testBug15947_IpTooLong()
    {
        $testIp = '2001:0ec8:0000:0000:0000:0000:0000:0001111';

        $is = $this->ip->checkIPv6($testIp);

        $this->assertFalse($is);

    }

    /**
     * handle Bug 18976
     * checkIPv6 did not check the value of the netmask
     *
     * @return void
     */
    public function testBug18976_NetmaskValueOutOfRange()
    {
        $testIp = '2002::/129';

        $is = $this->ip->checkIPv6($testIp);

        $this->assertFalse($is);
    }

    /**
     * Test covers bugfix in pear/Net_IPv6#6.
     *
     * @return void
     * @link https://github.com/pear/Net_IPv6/pull/5
     */
    public function testPfsenseFix()
    {
        $this->assertFalse($this->ip->checkIPv6("2345::1/-1"));
    }
}
