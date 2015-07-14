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
 * Class ToolEnv
 * @package SmetDenis\JBDump
 */
class ToolEnv extends Tool
{
    /**
     * Show $_ENV array
     * @return  JBDump
     */
    public static function env()
    {
        if (!self::isDebug()) {
            return false;
        }

        return self::i()->dump($_ENV, '! $_ENV !');
    }
}