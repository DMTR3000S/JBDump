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
 * Class ToolSession
 * @package SmetDenis\JBDump
 */
class ToolSession extends Tool
{
    /**
     * Show $_SESSION array
     * @return  JBDump
     */
    public static function session()
    {
        $sessionId = session_id();
        if (!$sessionId) {
            $_SESSION  = 'PHP session don\'t start';
            $sessionId = '';
        } else {
            $sessionId = ' (' . $sessionId . ') ';
        }

        return self::i()->dump($_SESSION, '! $_SESSION ' . $sessionId . ' !');
    }
}