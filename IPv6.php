<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Alexander Merz <alexander.merz@t-online.de>				  |
// +----------------------------------------------------------------------+
//
// $Id$

/**
* Class to validate and to work with IPv6
*
* @author  Alexander Merz <alexander.merz@t-online.de>
* @package Net_IPv6
* @version $Id$
* @access  public
*/
class Net_IPv6 {

    // {{{ Uncompress()
    	
    /**
     * Uncompresses an IPv6 adress
     * 
     * RFC 2373 allows you to compress zeros in an adress to '::'. This
     * function expects an valid IPv6 adress and expands the '::' to 
     * the required zeros.
     * 
     * Example:  FF01::101	->  FF01:0:0:0:0:0:0:101
     *           ::1        ->  0:0:0:0:0:0:0:1 
     *
     * @access public
     * @see Compress()
     * @static
     * @param string $ip	a valid IPv6-adress
     * @return string	the uncompressed IPv6-adress	
	 */
    function Uncompress( $ip) {
        if( strstr($ip, '::') ) {
            $ipComp = str_replace( '::', ':', $ip) ;
            if( ':' == $ipComp{0} ) {
                $ipComp = substr( $ipComp, 1) ;
            }

            $ipParts = count( explode( ':', $ipComp)) ;
            if( strstr( $ip, '.') ) {
                $ipParts++ ;
            }

            $ipMiss = "" ;
            for( $i = 0; (8 - $ipParts) > $i; $i++) {
                $ipMiss = $ipMiss.'0:' ;
            }
            if( 0 != strpos( $ip, '::') ) {
                $ipMiss = ':'.$ipMiss ;
            }

            $ip = str_replace( '::', $ipMiss, $ip) ;
        }

        return $ip ;		
    }
    
    // }}}
    // {{{ Compress()

    /**
     * Compresses an IPv6 adress
     * 
     * RFC 2373 allows you to compress zeros in an adress to '::'. This
     * function expects an valid IPv6 adress and compresses successive zeros
     * to '::'
     * 
     * Example:  FF01:0:0:0:0:0:0:101 	-> FF01::101
     *           0:0:0:0:0:0:0:1        -> ::1 
     *
     * @access public
     * @see Uncompress()
     * @static	
     * @param string $ip	a valid IPv6-adress
     * @return string	the compressed IPv6-adress	
     */
    function Compress( $ip)	{

        if( !strstr( $ip, "::")) {
            $ipPart = explode( ":", $ip) ;
            $ipComp = "" ;
            $flag = true ;
            for( $i = 0; $i < count( $ipPart); $i++) {
                if( !$ipPart[$i] and !$ipPart[$i+1]) {
                    break ;
                } else {
                    $ipComp = $ipComp.$ipPart[$i].":" ;
                }
            }
            $ipComp = substr( $ipComp, 0, -1) ;			
            for( ; $i < count( $ipPart); $i++) {
                if( $flag ) {
                    $flag = !$flag ;
                    $ipComp = $ipComp."::" ;
                }
                if( 0 != $ipPart[$i] ) {
                    break ;
                }
            }

            for( ; $i < count( $ipPart); $i++) {
                $ipComp = $ipComp.$ipPart[$i].":" ;
            }
        }
        if( '::' == substr( $ipCom, strlen( $ipcom)-2 ) ) {
            $ip = substr( $ipComp, 0, -1) ;
        } else {
            $ip = $ipComp ;			
        }
        return $ip ;
		
    }
    
    // }}}
    // {{{ SplitV64()

    /**
     * Splits an IPv6 adress into the IPv6 and a possible IPv4 part
     * 
     * RFC 2373 allows you to note the last two parts of an IPv6 adress as 
     * an IPv4 compatible adress
     * 
     * Example:  0:0:0:0:0:0:13.1.68.3
     *           0:0:0:0:0:FFFF:129.144.52.38
     *
     * @access public
     * @static	
     * @param string $ip	a valid IPv6-adress
     * @return array		[0] contains the IPv6 part, [1] the IPv4 part
     */
    function SplitV64( $ip) {
        $ip = Net_IPv6::Uncompress( $ip) ;			
        if( strstr( $ip, '.')) {

            $pos = strrpos( $ip, ':') ;
            $ip{ $pos} = '_' ;
            $ipPart = explode( '_', $ip) ;
            return $ipPart ;
        } else {
            return array( $ip, "") ;
        }
    }
    
    // }}}
    // {{{ checkIPv6

    /**
     * Checks an IPv6 adress 
     * 
     * Checks if the given IP is IPv6-compatible 
     * 
     * @access public
     * @static	
     * @param string $ip	a valid IPv6-adress
     * @return boolean	true if $ip is an IPv6 adress
     */
    function checkIPv6( $ip) {

        $ipPart = Net_IPv6::SplitV64( $ip) ;
        $count = 0 ;
        if( !empty( $ipPart[0]) ) {
            $ipv6 = explode( ':', $ipPart[0]) ;

            for ($i = 0; $i < count( $ipv6); $i++) {
                if ($ipv6[$i] >= 0x0 && $ipv6[$i] <= 0xFFFF ) {
                    $count++;
                }
            }
            if( 8 == $count ) {
                return true ;
            } elseif(  6 == $count and !empty( $ipPart[1])) {
                $ipv4 = explode( '.',$ipPart[1]) ;
                $count = 0 ;
                for ($i = 0; $i < count( $ipv4); $i++) {
                    if ($ipv4[$i] >= 0 && $ipv4[$i] <= 255 && preg_match("/^\d{1,3}$/", $ipv4[$i])) {
                        $count++;
                    }
                }
                if( 4 == $count) {
                    return true ;
                }
            } else {
                return false ;
            }

        } else {
            return false ;
        }
		
    }
    
    // }}}
	
}
?>
