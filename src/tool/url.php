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
 * Class ToolInterfaces
 * @package SmetDenis\JBDump
 */
class ToolUrl extends Tool
{
    /**
     * Parse url
     * @param   string $url     URL string
     * @param   string $varname URL name
     * @return  JBDump
     */
    public static function url($url, $varname = '...')
    {
        if (!self::isDebug()) {
            return false;
        }

        $parsed = parse_url($url);

        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $parsed['query_parsed']);
        }

        return self::i()->dump($parsed, $varname);
    }

}
