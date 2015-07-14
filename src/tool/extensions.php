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
 * Class ToolExtensions
 * @package SmetDenis\JBDump
 */
class ToolExtensions extends Tool
{
    /**
     * Show loaded PHP extensions
     * @param   bool $zend Get only Zend extensions
     * @return  JBDump
     */
    public static function extensions($zend = false)
    {
        if (!self::isDebug()) {
            return false;
        }
        return self::i()->dump(get_loaded_extensions($zend), '! extensions ' . ($zend ? '(Zend)' : '') . ' !');
    }
}