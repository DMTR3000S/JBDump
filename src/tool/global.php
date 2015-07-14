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
 * Class ToolGlobal
 * @package SmetDenis\JBDump
 */
class ToolGlobal extends Tool
{
    /**
     * Show $GLOBALS array
     * @return  JBDump
     */
    public static function globals()
    {
        if (!self::isDebug()) {
            return false;
        }

        return self::i()->dump($GLOBALS, '! $GLOBALS !');
    }
}