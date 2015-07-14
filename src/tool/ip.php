<?php
/**
 * JBDump
 *
 * Copyright (c) 2015, Denis Smetannikov <denis@jbzoo.com>.
 *
 * @package   JBDump
 * @author    Denis Smetannikov <denis@jbzoo.com>
 * @copyright 2015 Denis Smetannikov <denis@jbzoo.com>
 * @link      http://github.com/smetdenis/jbdump
 */

namespace SmetDenis\JBDump;

/**
 * Class ToolIP
 * @package SmetDenis\JBDump
 */
class ToolIP extends Tool
{
    /**
     * Show client IP
     * @return  JBDump
     */
    public static function ip()
    {
        if (!self::isDebug()) {
            return false;
        }

        $ip = self::getClientIP();

        $data = array(
            'ip'        => $ip,
            'host'      => gethostbyaddr($ip),
            'source'    => '$_SERVER["' . self::getClientIP(true) . '"]',
            'inet_pton' => inet_pton($ip),
            'ip2long'   => ip2long($ip),
        );

        return self::i()->dump($data, '! my IP = ' . $ip . ' !');
    }

}
