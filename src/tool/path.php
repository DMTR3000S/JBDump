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
 * Class ToolPath
 * @package SmetDenis\JBDump
 */
class ToolPath extends Tool
{
    /**
     * Show included and system paths
     * @return  JBDump
     */
    public static function path()
    {
        if (!self::isDebug()) {
            return false;
        }

        $result = array(
            'get_include_path' => explode(PATH_SEPARATOR, trim(get_include_path(), PATH_SEPARATOR)),
            '$_SERVER[PATH]'   => explode(PATH_SEPARATOR, trim($_SERVER['PATH'], PATH_SEPARATOR))
        );

        return self::i()->dump($result, '! paths !');
    }
}