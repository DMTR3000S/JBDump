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
class ToolInterfaces extends Tool
{
    /**
     * Show declared interfaces
     * @return  JBDump
     */
    public static function interfaces()
    {
        if (!self::isDebug()) {
            return false;
        }

        return self::i()->dump(get_declared_interfaces(), '! interfaces !');
    }

}