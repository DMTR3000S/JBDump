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
 * Class ToolPhpIni
 * @package SmetDenis\JBDump
 */
class ToolPhpIni extends Tool
{
    /**
     * Show php.ini content (open php.ini file)
     * @return  JBDump
     */
    public static function phpini()
    {
        if (!self::isDebug()) {
            return false;
        }

        $data = get_cfg_var('cfg_file_path');
        if (!@file($data)) {
            return false;
        }
        $ini = parse_ini_file($data, true);
        return self::i()->dump($ini, '! php.ini !');
    }
}