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
 * Class ToolMethods
 * @package SmetDenis\JBDump
 */
class ToolMethods extends Tool
{
    /**
     * Dump info about class (object)
     * @param   string|object $data Object or class name
     * @return  JBDump
     */
    public static function methods($data)
    {
        $result = self::_getClass($data);
        if ($result) {
            $data = $result['name'];
        }

        return self::i()->dump($result, '! class (' . $data . ') !');
    }
}