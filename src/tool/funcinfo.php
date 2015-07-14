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
 * Class ToolFuncInfo
 * @package SmetDenis\JBDump
 */
class ToolFuncInfo extends Tool
{
    /**
     * Dump all info about function
     * @param   string|Closure $functionName Closure or function name
     * @return  JBDump
     */
    public static function funcInfo($functionName)
    {
        $result = self::_getFunction($functionName);
        if ($result) {
            $functionName = $result['name'];
        }
        return self::i()->dump($result, '! function (' . $functionName . ') !');
    }
}