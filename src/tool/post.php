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
 * Class ToolPost
 * @package SmetDenis\JBDump
 */
class ToolPost extends Tool
{
    /**
     * Show $_POST array
     * @return  JBDump
     */
    public static function post()
    {
        if (!self::isDebug()) {
            return false;
        }
        return self::i()->dump($_POST, '! $_POST !');
    }
}