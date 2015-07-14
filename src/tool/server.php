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
 * Class ToolServer
 * @package SmetDenis\JBDump
 */
class ToolServer extends Tool
{
    /**
     * Show $_SERVER array
     * @return  JBDump
     */
    public static function server()
    {
        if (!self::isDebug()) {
            return false;
        }
        return self::i()->dump($_SERVER, '! $_SERVER !');
    }
}