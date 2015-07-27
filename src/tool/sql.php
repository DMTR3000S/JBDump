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
class ToolSql extends Tool
{
    /**
     * Highlight SQL query
     * @param        $query
     * @param string $sqlName
     * @param bool   $nl2br
     * @return JBDump
     */
    public static function sql($query, $sqlName = 'SQL Query', $nl2br = false)
    {
        // Joomla hack
        if (defined('_JEXEC')) {
            $config = new JConfig();
            $prefix = $config->dbprefix;
            $query  = str_replace('#__', $prefix, $query);
        }

        if (class_exists('JBDump_SqlFormatter')) {
            $sqlHtml = JBDump_SqlFormatter::format($query);
            return self::i()->dump($sqlHtml, $sqlName . '::html');
        }

        return self::i()->dump($query, $sqlName . '::html');
    }
}