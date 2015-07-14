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
 * Class ToolConf
 * @package SmetDenis\JBDump
 */
class ToolConf extends Tool
{
    /**
     * Show php.ini content (PHP API)
     * @param   string $extension Extension name
     * @param   bool   $details   Retrieve details settings or only the current value for each setting
     * @return  bool|JBDump
     */
    public static function conf($extension = '', $details = true)
    {
        if (!self::isDebug()) {
            return false;
        }

        if ($extension == '') {
            $label = '';
            $data  = ini_get_all();
        } else {
            $label = ' (' . $extension . ') ';
            $data  = ini_get_all($extension, $details);
        }

        return self::i()->dump($data, '! configuration settings' . $label . ' !');
    }
}