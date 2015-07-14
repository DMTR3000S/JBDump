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
 * Class ToolRequest
 * @package SmetDenis\JBDump
 */
class ToolRequest extends Tool
{
    /**
     * Show $_REQUEST array or dump $_GET, $_POST, $_COOKIE
     * @param bool $notReal Get real $_REQUEST array
     * @return bool|JBDump
     */
    public static function request($notReal = false)
    {
        if (!self::isDebug()) {
            return false;
        }

        if ($notReal) {
            self::get();
            self::post();
            self::cookie();
            return self::files();
        } else {
            return self::i()->dump($_REQUEST, '! $_REQUEST !');
        }
    }
}