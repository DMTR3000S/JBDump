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
 * Class ToolClasses
 * @package SmetDenis\JBDump
 */
class ToolClasses extends Tool
{
    /**
     * Show declared classes
     * @param bool $sort
     * @return bool|JBDump
     */
    public static function classes($sort = false)
    {
        if (!self::isDebug()) {
            return false;
        }

        $classes = get_declared_classes();
        if ((bool)$sort) {
            sort($classes);
        }

        return self::i()->dump($classes, '! classes !');
    }
}