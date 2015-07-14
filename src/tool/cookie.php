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
 * Class ToolCookie
 * @package SmetDenis\JBDump
 */
class ToolCookie extends Tool
{
    /**
     * Show $_COOKIE array
     * @return  JBDump
     */
    public static function cookie()
    {
        if (!self::isDebug()) {
            return false;
        }
        return self::i()->dump($_COOKIE, '! $_COOKIE !');
    }
}