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
 * Class ToolTimestamp
 * @package SmetDenis\JBDump
 */
class ToolTimestamp extends Tool
{
    /**
     * Convert timestamp to normal date, in DATE_RFC822 format
     * @param   null|integer $timestamp Time in Unix timestamp format
     * @return  bool|JBDump
     */
    public static function timestamp($timestamp = null)
    {
        if (!self::isDebug()) {
            return false;
        }

        $date = date(DATE_RFC822, $timestamp);
        return self::i()->dump($date, $timestamp . ' sec = ');
    }
}