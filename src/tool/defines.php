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
 * Class ToolDefines
 * @package SmetDenis\JBDump
 */
class ToolDefines extends Tool
{
    /**
     * Show defined constants
     * @param bool $showAll Get only user defined functions
     * @return bool|JBDump
     */
    public static function defines($showAll = false)
    {
        if (!self::isDebug()) {
            return false;
        }

        $defines = get_defined_constants(true);
        if (!$showAll) {
            $defines = (isset($defines['user'])) ? $defines['user'] : array();
        }

        return self::i()->dump($defines, '! defines !');
    }
}