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
 * Class ToolFunctions
 * @package SmetDenis\JBDump
 */
class ToolFunctions extends Tool
{
    /**
     * Show defined functions
     * @param   bool $showInternal Get only internal functions
     * @return  JBDump
     */
    public static function functions($showInternal = false)
    {
        if (!self::isDebug()) {
            return false;
        }

        $functions = get_defined_functions();
        if ($showInternal) {
            $functions = $functions['internal'];
            $type      = 'internal';
        } else {
            $functions = $functions['user'];
            $type      = 'user';
        }

        return self::i()->dump($functions, '! functions (' . $type . ') !');
    }
}