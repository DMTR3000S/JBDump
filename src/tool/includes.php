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
 * Class ToolIncludes
 * @package SmetDenis\JBDump
 */
class ToolIncludes extends Tool
{
    /**
     * Show included files
     * @return  JBDump
     */
    public static function includes()
    {
        if (!self::isDebug()) {
            return false;
        }

        return self::i()->dump(get_included_files(), '! includes files !');
    }
}