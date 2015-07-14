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
 * Class ToolTimezone
 * @package SmetDenis\JBDump
 */
class ToolTimezone extends Tool
{
    /**
     * Show date default timezone
     * @return  JBDump
     */
    public static function timezone()
    {
        if (!self::isDebug()) {
            return false;
        }

        $data = date_default_timezone_get();
        return self::i()->dump($data, '! timezone !');
    }
}