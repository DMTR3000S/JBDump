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
 * Class ToolLocale
 * @package SmetDenis\JBDump
 */
class ToolLocale extends Tool
{
    /**
     * Find all locale in system
     * list - only for linux like systems
     * @return  JBDump
     */
    public static function locale()
    {
        if (!self::isDebug()) {
            return false;
        }

        ob_start();
        @system('locale -a');
        $locale = explode(PHP_EOL, trim(ob_get_contents()));
        ob_end_clean();

        $result = array(
            'list' => $locale,
            'conv' => @localeconv()
        );

        return self::i()->dump($result, '! locale info !');
    }
}